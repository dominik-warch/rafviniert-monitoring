<?php

namespace App\Http\Controllers;

class CalculationController extends Controller
{
    public function showCalculations()
    {
        return view('calculations.calculations');
    }
}
