<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferenceGeometry\StoreRequest;
use App\Models\ReferenceGeometry;
use Clickbar\Magellan\Data\Geometries\MultiPolygon;
use Clickbar\Magellan\Data\Geometries\Polygon;
use Clickbar\Magellan\IO\Parser\Geojson\GeojsonParser;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class ImportReferenceGeometriesController extends Controller
{
    public function create(): View
    {
        return view('import.reference-geometries.create');
    }

    public function store(StoreRequest $request)
    {
        $geojsonData = file_get_contents($request->file('geojson')->getRealPath());
        $geojson = json_decode($geojsonData, true);

        $geoJsonParser = app(GeoJsonParser::class);

        foreach ($geojson['features'] as $feature) {
            $geoJsonFeatureString = json_encode($feature['geometry']);

            // Using Magellan's GeoJsonParser to convert to Magellan's geometries
            $geometry = $geoJsonParser->parse($geoJsonFeatureString);

            if ($geometry instanceof Polygon) {
                // Convert to MultiPolygon
                $multiPolygon = MultiPolygon::make([$geometry]);
            } elseif ($geometry instanceof MultiPolygon) {
                $multiPolygon = $geometry;
            } else {
                // Skip any other geometry types
                continue;
            }

            // Store in database
            ReferenceGeometry::create([
                'name' => $request->input('name'),
                'geometry' => $multiPolygon,
            ]);
        }

        // Redirect to success page
        return Redirect::route('import.reference-geometries.create')->success('Import erfolgreich abgeschlossen!');
    }
}
