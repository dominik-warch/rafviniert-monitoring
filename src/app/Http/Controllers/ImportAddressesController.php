<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Clickbar\Magellan\IO\Parser\Geojson\GeojsonParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class ImportAddressesController extends Controller
{
    public function create(): View
    {
        return view('import.addresses.create');
    }

    public function store(Request $request)
    {
        $geojsonData = file_get_contents($request->file('geojson')->getRealPath());
        $geojson = json_decode($geojsonData, true);

        $geoJsonParser = app(GeoJsonParser::class);

        foreach ($geojson['features'] as $feature) {
            $geoJsonFeatureGeometryString = json_encode($feature['geometry']);

            // Using Magellan's GeoJsonParser to convert to Magellan's geometries
            $geometry = $geoJsonParser->parse($geoJsonFeatureGeometryString);

            // Store in database
            Address::create([
                'zip_code' => $feature['properties']['plz'],
                'city' => $feature['properties']['ortsgemeinde'],
                'street' => $feature['properties']['strassenname'],
                'housenumber' => $feature['properties']['hausnummer'],
                'housenumber_ext' => $feature['properties']['adressierungszusatz'],
                'geometry' => $geometry,
            ]);
        }

        // Redirect to success page
        return Redirect::route('import.addresses.create')->success('Import erfolgreich abgeschlossen!');
    }
}
