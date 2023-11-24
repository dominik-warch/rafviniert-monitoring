<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateAgedDependencyRatio;
use App\Jobs\CalculateChildDependencyRatio;
use App\Jobs\CalculateGreyingIndex;
use App\Jobs\CalculateMedianAge;
use App\Jobs\CalculateQualifyingResidentsAgeGroup;
use App\Jobs\CalculateQualifyingResidentsGender;
use App\Jobs\CalculateTotalDependencyRatio;
use Illuminate\Http\Request;
use App\Jobs\CalculateRemanenceBuilding;
use App\Jobs\CalculateMeanAge;
use App\Models\ReferenceGeometry;
use App\Models\CitizensMaster;

class CalculationController extends Controller
{
    public function showCalculations()
    {
        return view('calculations.calculations');
    }
}
