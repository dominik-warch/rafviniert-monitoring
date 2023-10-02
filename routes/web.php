<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
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

    Route::get('/import/citizens_master', [ImportController::class, 'create'])->name('import.citizens-master.create');
    Route::post('/import/citizens_master', [ImportController::class, 'store'])->name('import.citizens-master.store');
    Route::get('/import/citizens_master/mapping', [ImportController::class, 'mapping'])->name('import.citizens-master.mapping.create');
    Route::post('/import/citizens_master/mapping', [ImportController::class, 'storeMapping'])->name('import.citizens-master.mapping.store');




});

Route::get('/map', function () {
    return Inertia::render('Map');
})->name('map');
