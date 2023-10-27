<?php
namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\Log;

class LocalGeocodingService
{
    public function geocode($zipCode, $city, $street, $houseNumber, $houseNumberExtra)
    {
        $searchAddress = strtolower("$street $houseNumber$houseNumberExtra, $zipCode, $city");

        $localAddress = Address::query()
            ->where("zip_code", $zipCode)
            ->where("city", $city)
            ->where("street", $street)
            ->where("housenumber", $houseNumber)
            ->when($houseNumberExtra, function ($query, $houseNumberExtra) {
                return $query->where("housenumber_ext", $houseNumberExtra);
            })
            ->first();

        if ($localAddress) {
            Log::debug("Found local match for address: " . $searchAddress);
            return $localAddress["geometry"];
        }

        Log::debug("No local match found for address: " . $searchAddress);
        return null;
    }
}
