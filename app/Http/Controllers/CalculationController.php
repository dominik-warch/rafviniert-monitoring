<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\CalculateMedianAge;
use App\Models\ReferenceGeometry;
use App\Models\CitizensMaster;

class CalculationController extends Controller
{
    public function showCalculations()
    {
        $referenceGeometries = ReferenceGeometry::distinct('name')->pluck('name');
        $datasetDates = CitizensMaster::distinct('dataset_date')->pluck('dataset_date');

        return view('calculations.calculations', [
            'referenceGeometries' => $referenceGeometries,
            'datasetDates' => $datasetDates,
        ]);
    }

    public function calculateMedianAge(Request $request)
    {
        $referenceGeometry = $request->input('reference_geometry');
        $dateOfDataset = $request->input('date_of_dataset');

        CalculateMedianAge::dispatch($referenceGeometry, $dateOfDataset);

        return redirect()->back()->with('message', 'Calculation started!');
    }
}
