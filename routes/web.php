<?php

use App\Http\Controllers\CalculationController;
use App\Http\Controllers\ImportCitizensTransactionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportCitizensMasterController;
use App\Http\Controllers\ImportAddressesController;
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

    Route::get('/import/citizens_master', [ImportCitizensMasterController::class, 'create'])->name('import.citizens-master.create');
    Route::post('/import/citizens_master', [ImportCitizensMasterController::class, 'store'])->name('import.citizens-master.store');
    Route::get('/import/citizens_master/mapping', [ImportCitizensMasterController::class, 'mapping'])->name('import.citizens-master.mapping.create');
    Route::post('/import/citizens_master/mapping', [ImportCitizensMasterController::class, 'storeMapping'])->name('import.citizens-master.mapping.store');

    Route::get('/import/citizens_transaction', [ImportCitizensTransactionController::class, 'create'])->name('import.citizens-transaction.create');
    Route::post('/import/citizens_transaction', [ImportCitizensTransactionController::class, 'store'])->name('import.citizens-transaction.store');
    Route::get('/import/citizens_transaction/mapping', [ImportCitizensTransactionController::class, 'mapping'])->name('import.citizens-transaction.mapping.create');
    Route::post('/import/citizens_transaction/mapping', [ImportCitizensTransactionController::class, 'storeMapping'])->name('import.citizens-transaction.mapping.store');

    Route::get('/import/reference_geometries', [ImportAddressesController::class, 'create'])->name('import.reference-geometries.create');
    Route::post('/import/reference_geometries', [ImportAddressesController::class, 'store'])->name('import.reference-geometries.store');

    Route::get('/import/addresses', [ImportAddressesController::class, 'create'])->name('import.addresses.create');
    Route::post('/import/addresses', [ImportAddressesController::class, 'store'])->name('import.addresses.store');

    Route::get('/calculations', [CalculationController::class, 'showCalculations'])->name('calculations.show-calculations');
    Route::post('/calculations', [CalculationController::class, 'calculate'])->name('calculations.calculate');

    Route::get('/map', function () {return Inertia::render('Map');})->name('map');
});


