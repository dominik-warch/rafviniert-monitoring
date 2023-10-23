<?php

namespace App\Imports;

use App\Models\CitizensMaster;
use Clickbar\Magellan\Data\Geometries\Point;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class CitizensMasterImport implements ToModel, WithChunkReading, WithHeadingRow, WithCustomCsvSettings
{
    protected array $columnMapping;
    protected $dataset_date;
    protected Client $httpClient;

    public function __construct(array $columnMapping, $dataset_date)
    {
        $this->columnMapping = $columnMapping;
        $this->dataset_date = $dataset_date;
        $this->httpClient = new Client();
    }

    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row): ?CitizensMaster
    {
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

        $gender = $this->parseGender($row[$this->columnMapping["gender"]]);
        $yearOfBirth = $this->parseYearOfBirth($row[$this->columnMapping["year_of_birth"]]);

        return new CitizensMaster([
            "gender" => $gender,
            "year_of_birth" => $yearOfBirth,
            "dataset_date" => $this->dataset_date,
            "zip_code" => $row[$this->columnMapping["zip_code"]],
            "city" => $row[$this->columnMapping["city"]],
            "street" => $row[$this->columnMapping["street"]],
            "housenumber" => $row[$this->columnMapping["housenumber"]],
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

    protected function parseYearOfBirth($rawDate): int
    {
        if (is_numeric($rawDate)) {
            return intval($rawDate); // Return if it's already a year
        }

        $date = new \DateTime($rawDate);
        return (int) $date->format('Y'); // Extract year from the date
    }

    protected function geocodeAddress($zipCode, $city, $street, $houseNumber, $houseNumberExtra)
    {
        // Implement the logic to geocode the address
        $address = urlencode("$street $houseNumber $houseNumberExtra, $city, $zipCode");
        $url = "https://nominatim.rafviniert.de/search?q=$address";
        $maxRetries = 3;
        $retryDelay = 200000; // 200 milliseconds

        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                $response = $this->httpClient->get($url);
                $body = $response->getBody();
                $data = json_decode($body, true);

                if (isset($data[0]["lat"])) {
                    Log::info("Successful geocode address: " . $address);
                    return $data[0];
                } else {
                    Log::warning("Failed to geocode address: " . $address);
                }

            } catch (RequestException $e) {
                Log::warning("Attempt " . ($i+1) . ": " . $e->getMessage());
                if ($e->getResponse() && $e->getResponse()->getStatusCode() == 429 && $i < $maxRetries - 1) {
                    usleep($retryDelay); // Wait before retrying if rate limited
                }
            }
        }

        // After all retries failed, return default coordinates
        Log::error("All geocoding attempts failed for address: $address");
        return ["lat" => 0, "lon" => 0];
    }

    protected function validateRow(array $row): bool
    {
        $validationRules = [
            $this->columnMapping["gender"] => 'required|string',
            $this->columnMapping["year_of_birth"] => 'required|numeric|between:1900,' . date("Y"),
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
