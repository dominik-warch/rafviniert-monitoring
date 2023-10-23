<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\CalculateMedianAge;
use App\Jobs\CalculateMeanAge;
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

    public function calculate(Request $request)
    {
        $referenceGeometry = $request->input('reference_geometry');
        $dateOfDataset = $request->input('date_of_dataset');
        $calculationType = $request->input('calculation_type');

        switch ($calculationType) {
            case "median":
                CalculateMedianAge::dispatch($referenceGeometry, $dateOfDataset);
                break;
            case "mean":
                CalculateMeanAge::dispatch($referenceGeometry, $dateOfDataset);
                break;
            default:
                return redirect()->back()->withErrors(["Umknown calculation type"]);
        }

        return redirect()->back()->with('message', 'Calculation started!');
    }
}
