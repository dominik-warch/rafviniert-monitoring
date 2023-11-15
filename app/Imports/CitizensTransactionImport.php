<?php

namespace App\Imports;

use App\Models\CitizensTransaction;
use App\Services\DataParsingService;
use Carbon\Carbon;
use Clickbar\Magellan\Data\Geometries\Point;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class CitizensTransactionImport implements ToModel, WithChunkReading, WithHeadingRow, WithCustomCsvSettings
{
    protected array $columnMapping;
    protected string $dataset_date;
    protected string $transaction_type;

    public function __construct(
        array $columnMapping,
        string $dataset_date,
        string $fileExtension,
        $transaction_type,
        $localGeocodingService,
        $externalGeocodingService)
    {
        $this->columnMapping = $columnMapping;
        $this->dataset_date = $dataset_date;
        $this->fileExtension = $fileExtension;
        $this->transaction_type = $transaction_type;
        $this->localGeocodingService = $localGeocodingService;
        $this->externalGeocodingService = $externalGeocodingService;
    }

    /**
     * @param array $row
     *
     * @return Model|null
     * @throws Exception
     */
    public function model(array $row): ?CitizensTransaction
    {
        $row = array_change_key_case($row, CASE_LOWER);

        // Validate the row first
        if (!$this->validateRow($row)) {
            Log::warning("Skipped invalid row: ", $row);
            return null; // Skip this row
        }

        $geocodedAddress = $this->geocodeAddress(
            $row[$this->columnMapping["zip_code"]],
            $row[$this->columnMapping["city"]],
            $row[$this->columnMapping["street"]],
            $row[$this->columnMapping["housenumber"]],
            $row[$this->columnMapping["housenumber_extra"]]
        );

        $transactionType = (isset($this->transaction_type)) ? $this->transaction_type : $row[$this->columnMapping["transaction_type"]];
        $transactionDate = $this->parseTransactionDate($row[$this->columnMapping["transaction_date"]]);
        $gender = $this->parseGender($row[$this->columnMapping["gender"]]);
        $yearOfBirth = $this->parseYearOfBirth($row[$this->columnMapping["year_of_birth"]]);

        return new CitizensTransaction([
            "transaction_date" => $transactionDate,
            "transaction_type" => $transactionType,
            "gender" => $gender,
            "year_of_birth" => $yearOfBirth,
            "dataset_date" => $this->dataset_date,
            "zip_code" => $row[$this->columnMapping["zip_code"]],
            "city" => $row[$this->columnMapping["city"]],
            "street" => $row[$this->columnMapping["street"]],
            "housenumber" => (string) $row[$this->columnMapping["housenumber"]],
            "housenumber_ext" => $row[$this->columnMapping["housenumber_extra"]],
            "geometry"=> Point::make($geocodedAddress["lon"], $geocodedAddress["lat"])
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getCsvSettings(): array
    {
        return [
            "input_encoding" => "ISO-8859-1"
        ];
    }

    /**
     * @throws Exception
     */
    protected function parseTransactionDate(string $rawDate): Carbon
    {
        try {
            return Carbon::parse($rawDate);
        } catch (Exception $e) {
            Log::error("Could not parse the date: " . $e->getMessage());
            throw $e;
        }
    }

    protected function parseGender(string $rawGender): string
    {
        $genderMap = [
            "male" => "m",
            "man" => "m",
            "männlich" => "m",
            "maennlich" => "m",
            "mann" => "m",
            "m" => "m",
            "female" => "w",
            "woman" => "w",
            "weiblich" => "w",
            "frau" => "w",
            "w" => "w",
            "diverse" => "d",
            "divers" => "d",
            "d" => "d"
        ];

        $normalizedGender = strtolower(trim($rawGender));
        return $genderMap[$normalizedGender] ?? "d"; // Default to 'd' if unknown gender
    }

    /**
     * @throws Exception
     */
    protected function parseYearOfBirth($rawDate): int
    {
        $dateParsingService = new DataParsingService();
        return $dateParsingService->parseYearOfBirth($rawDate, $this->fileExtension);
    }

    protected function geocodeAddress($zipCode, $city, $street, $houseNumber, $houseNumberExtra)
    {
        $localResult = $this->localGeocodingService->geocode($zipCode, $city, $street, $houseNumber, $houseNumberExtra);
        if ($localResult !== null) {
            Log::info("Address geocoded locally");
            return $localResult;
        }

        $externalResult = $this->externalGeocodingService->geocode($zipCode, $city, $street, $houseNumber, $houseNumberExtra);
        if ($externalResult !== null) {
            Log::info("Address geocoded externally");
            return $externalResult;
        }

        Log::warning("Geocoding failed for address: $street $houseNumber $houseNumberExtra, $city, $zipCode");
        return ["lat" => 0, "lon" => 0];
    }

    protected function validateRow(array $row): bool
    {
        $validationRules = [
            $this->columnMapping["transaction_type"] => 'nullable',
            $this->columnMapping["transaction_date"] => 'required',
            $this->columnMapping["gender"] => 'required|string',
            $this->columnMapping["year_of_birth"] => 'required',
            $this->columnMapping["zip_code"] => 'required|alpha_num',
            $this->columnMapping["city"] => 'required|string|max:255',
            $this->columnMapping["street"] => 'required|string|max:255',
            $this->columnMapping["housenumber"] => 'required|regex:/^[a-zA-Z0-9\-]+$/',
            $this->columnMapping["housenumber_extra"] => 'nullable|string'
        ];

        $validator = Validator::make($row, $validationRules);

        if ($validator->fails()) {
            Log::warning("Row validation failed. Errors: ", $validator->errors()->all());
            return false;
        }

        return true;
    }
}
