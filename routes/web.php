<?php

use App\Actions\Importer\ParsePharmacyAvailabilityCsvFiles;
use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return app(ParsePharmacyAvailabilityCsvFiles::class)->handle(
        collect(File::glob(storage_path('app/mohfiles/City_*.csv')))
    );

    return view('welcome');
});

Route::get('/pharmacies', [PharmacyController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/pharmacies/create', [PharmacyController::class, 'create']);
    Route::post('/pharmacies', [PharmacyController::class, 'store']);
    Route::get('/pharmacies/{pharmacy}', [PharmacyController::class, 'show']);
    Route::patch('/pharmacies/{pharmacy}', [PharmacyController::class, 'update']);
    Route::delete('/pharmacies/{pharmacy}', [PharmacyController::class, 'destroy']);
    Route::get('/pharmacies/{pharmacy}/edit', [PharmacyController::class, 'edit']);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
