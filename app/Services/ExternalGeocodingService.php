<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExternalGeocodingService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config("services.geocoding.base_url", "https://nominatim.rafviniert.de");
    }

    public function geocode($zipCode, $city, $street, $houseNumber, $houseNumberExtra)
    {
        $address = "$street $houseNumber $houseNumberExtra, $city, $zipCode";
        $url = "$this->baseUrl/search?q=" . urlencode($address);
        $maxRetries = 3;
        $retryDelay = 200000; // 200 milliseconds

        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                $response = Http::get($url);
                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data[0]["lat"])) {
                        Log::debug("Successful geocode address: " . $address);
                        return $data[0];
                    } else {
                        Log::debug("Failed to geocode address: " . $address);
                    }
                } else {
                    Log::debug("Request failed with status " . $response->status() . " for address: " . $address);
                }
            } catch (Throwable $e) {
                Log::debug("Attempt " . ($i + 1) . ": " . $e->getMessage());
            }

            // Add a delay before retrying
            if ($i < $maxRetries - 1) {
                usleep($retryDelay);
            }
        }


        Log::debug("All external geocoding attempts failed for address: " . $address );
        return null;
    }
}
