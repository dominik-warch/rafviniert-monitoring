<?php

namespace App\Imports;

use App\Models\CitizensMaster;
use Clickbar\Magellan\Data\Geometries\Point;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Carbon\Carbon;
use \ForceUTF8\Encoding;


class CitizensMasterImport implements ToModel, WithChunkReading, WithHeadingRow
{
    protected $columnMapping;
    protected $dataset_date;

    public function __construct(array $columnMapping, $dataset_date)
    {
        $this->columnMapping = $columnMapping;
        $this->dataset_date = $dataset_date;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $geocodedAddress = $this->geocodeAddress(
            $row[$this->columnMapping['zip_code']],
            $row[$this->columnMapping['city']],
            $row[$this->columnMapping['street']],
            $row[$this->columnMapping['housenumber']],
            $row[$this->columnMapping['housenumber_extra']]
        );

        return new CitizensMaster([
            'gender' => $row[$this->columnMapping['gender']],
            'year_of_birth' => $row[$this->columnMapping['year_of_birth']],
            'dataset_date' => $this->dataset_date,
            'zip_code' => $row[$this->columnMapping['zip_code']],
            'city' => Encoding::toUTF8($row[$this->columnMapping['city']]),
            'street' => Encoding::toUTF8($row[$this->columnMapping['street']]),
            'housenumber' => $row[$this->columnMapping['housenumber']],
            'housenumber_ext' => $row[$this->columnMapping['housenumber_extra']],
            'geometry'=> Point::make($geocodedAddress['lon'], $geocodedAddress['lat'])
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }

    protected function geocodeAddress($zipCode, $city, $street, $houseNumber, $houseNumberExtra)
    {
        // Implement the logic to geocode the address
        // This is an example using the Google Maps Geocoding API
        $address = urlencode("{$street} {$houseNumber} {$houseNumberExtra}, {$city}, {$zipCode}");
        $url = "https://nominatim.rafviniert.de/search?q={$address}";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data[0]['lat'])) {
            return $data[0];
        }

        // If the geocoding failed, return default coordinates
        return ['lat' => 0, 'lon' => 0];
    }
}
