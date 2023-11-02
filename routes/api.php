<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cars\CarsController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Parking\ParkingController;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('/parking', ParkingController::class);

    Route::get('/car/{parking}', [CarsController::class, 'index']);
    Route::get('/car/{parking}/{id}', [CarsController::class, 'show']);
    Route::post('/car', [CarsController::class, 'store']);
    Route::delete('/car/{parking}/{id}', [CarsController::class, 'destroy']);
    Route::patch('/car/{parking}/{id}', [CarsController::class, 'update']);
    Route::patch('/car/output/{parking}/{car}', [CarsController::class, 'registersCarExit']);

    Route::get('/employees/{parking}', [EmployeesController::class, 'index']);
    Route::get('/employees/{parking}/{id}', [EmployeesController::class, 'show']);
    Route::post('/employees', [EmployeesController::class, 'store']);
    Route::patch('/employees/{parking}/{id}', [EmployeesController::class, 'update']);
    Route::delete('/employees/{parking}/{id}', [EmployeesController::class, 'destroy']);
});
