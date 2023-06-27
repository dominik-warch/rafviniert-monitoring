<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
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

    Route::get('/upload', [UploadController::class, 'create'])->name('upload_form');
    Route::post('/upload', [UploadController::class, 'store'])->name('store_upload');
    Route::get('/mapping', [UploadController::class, 'mapping'])->name('mapping_form');
    Route::post('/mapping', [UploadController::class, 'storeMapping'])->name('store_mapping');




});
