<?php
namespace App\Services;

use App\Models\Address;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * LocalGeocodingService handles the geocoding of addresses using local database.
 */
class LocalGeocodingService
{
    /**
     * Geocode an address using local database.
     *
     * @param string $zipCode The postal code of the address.
     * @param string $city The city of the address.
     * @param string $street The street name of the address.
     * @param string $houseNumber The house number of the address.
     * @param string|null $houseNumberExtra Any additional information for the house number.
     * @return array|null Returns a Point geometry if found, null otherwise.
     */
    public function geocode(string $zipCode, string $city, string $street, string $houseNumber, ?string $houseNumberExtra): ?array
    {
        $searchAddress = strtolower("$street $houseNumber" . ($houseNumberExtra ? " $houseNumberExtra" : "") . ", $zipCode, $city");

        try {
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
                return ["lon" => $localAddress->geometry->getLongitude(), "lat" => $localAddress->geometry->getLatitude()];
            }
            Log::debug("No local match found for address: " . $searchAddress);
            return null;

        } catch (Exception $e) {
            Log::error("Failed to geocode local address: " . $searchAddress, ['exception' => $e]);
            return null;
        }
    }
}
