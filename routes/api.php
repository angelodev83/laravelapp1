<?php

use App\Http\Controllers\API\CountryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\RingCentralController;
use App\Http\Controllers\API\MgmtApp\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/v1/add/patient', [PatientController::class, 'addpatient']);
Route::post('/v1/add/order', [OrderController::class, 'addOrder']);

Route::get('/v1/get/country', [CountryController::class, 'get_country']);
// Route::get('/v1/ring-central/send-message', [RingCentralController::class, 'test_messaging']);
// Route::get('/v1/ring-central/fullsync-message', [RingCentralController::class, 'fullSync']);
// Route::get('/v1/ring-central/isync-message', [RingCentralController::class, 'iSync']);
// Route::get('/v1/ring-central/sync-message', [RingCentralController::class, 'sync']);
// Route::get('/v1/ring-central/getlist-message', [RingCentralController::class, 'getList']);

//Route::get('/v1/add_med', [PatientController::class, 'addMed']);

//MGMT88App
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
});

   