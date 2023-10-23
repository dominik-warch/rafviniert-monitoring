<?php

namespace App\Imports;

use App\Models\CitizensMaster;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class CitizensMasterImport implements ToModel, WithChunkReading, WithHeadingRow, WithCustomCsvSettings
{
    protected array $columnMapping;
    protected $dataset_date;

    public function __construct(array $columnMapping, $dataset_date)
    {
        $this->columnMapping = $columnMapping;
        $this->dataset_date = $dataset_date;
    }

    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row): CitizensMaster|null
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
        return $date->format('Y'); // Extract year from the date
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
                $response = @file_get_contents($url); // Suppressing warnings with @
                if ($response === false) {
                    throw new \Exception("Failed to fetch data from geocoding service.");
                }

                $data = json_decode($response, true);

                if (isset($data[0]["lat"])) {
                    Log::info("Successful geocode address: " . $address);
                    return $data[0];
                } else {
                    Log::warning("Failed to geocode address: " . $address);
                }

            } catch (\Exception $e) {
                Log::warning("Attempt " . ($i+1) . ": " . $e->getMessage());
                if ($i < $maxRetries - 1) {
                    usleep($retryDelay); // Wait before retrying
                }
            }
        }

        // After all retries failed, return default coordinates
        Log::error("All geocoding attempts failed for address: $address");
        return ["lat" => 0, "lon" => 0];
    }

    protected function validateRow(array $row): bool
    {
        // Define all the columns, where values are not required
        $nullableColumns = [
            $this->columnMapping["housenumber_extra"]
        ];

        // Check for missing columns
        foreach ($this->columnMapping as $key => $columnName) {
            // If the column is nullable, skip the presence check
            if (in_array($columnName, $nullableColumns)) {
                continue;
            }

            if (!isset($row[$columnName]) || (isset($row[$columnName]) && $row[$columnName] === "")) {
                Log::warning("Missing column $columnName in row: ", $row);
                return false;
            }
        }


        // Check for invalid data types or values
        // Ensure 'gender' is a string
        if (!is_string($row[$this->columnMapping["gender"]])) {
            Log::warning("Invalid data type for gender in row: ", $row);
            return false;
        }

        // Year of birth check
        if (!is_numeric($row[$this->columnMapping["year_of_birth"]]) ||
            $row[$this->columnMapping["year_of_birth"]] < 1900 ||
            $row[$this->columnMapping["year_of_birth"]] > date("Y")) {
            Log::warning("Invalid year_of_birth in row: ", $row);
            return false;
        }

        // ZIP code check (assuming alphanumeric ZIP, adjust as needed)
        if (!preg_match("/^[a-zA-Z0-9]+$/", $row[$this->columnMapping["zip_code"]])) {
            Log::warning("Invalid zip_code in row: ", $row);
            return false;
        }

        // City check
        if (!is_string($row[$this->columnMapping["city"]]) ||
            strlen($row[$this->columnMapping["city"]]) > 255) {
            Log::warning("Invalid city in row: ", $row);
            return false;
        }

        // Street check
        if (!is_string($row[$this->columnMapping["street"]]) ||
            strlen($row[$this->columnMapping["street"]]) > 255) {
            Log::warning("Invalid street in row: ", $row);
            return false;
        }

        // Housenumber check (accepting numeric, letters, and some special characters)
        $housenumber = (string) $row[$this->columnMapping["housenumber"]];
        if (!preg_match("/^[a-zA-Z0-9\-]+$/", $housenumber)) {
            Log::warning("Invalid housenumber in row: ", $row);
            return false;
        }

        // Housenumber extension check
        if (!is_null($row[$this->columnMapping["housenumber_extra"]]) &&
            !is_string($row[$this->columnMapping["housenumber_extra"]])) {
            Log::warning("Invalid housenumber_ext in row: ", $row);
            return false;
        }

        return true;
    }
}
