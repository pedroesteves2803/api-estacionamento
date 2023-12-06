<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cars\CarsController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Parking\ParkingController;
use App\Http\Controllers\Parking\VacanciesController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\User\DeviceController;
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
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register/device', [DeviceController::class, 'store'])->name('register.store');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('/parking', ParkingController::class)->names([
        'index'   => 'parking.index',
        'store'   => 'parking.store',
        'show'    => 'parking.show',
        'update'  => 'parking.update',
        'destroy' => 'parking.destroy',
    ]);

    Route::get('/car/{parking}', [CarsController::class, 'index'])->name('cars.index');
    Route::get('/car/{parking}/{id}', [CarsController::class, 'show'])->name('cars.show');
    Route::post('/car', [CarsController::class, 'store'])->name('cars.store');
    Route::delete('/car/{parking}/{id}', [CarsController::class, 'destroy'])->name('cars.destroy');
    Route::patch('/car/{parking}/{id}', [CarsController::class, 'update'])->name('cars.update');
    Route::patch('/car/output/{parking}/{car}', [CarsController::class, 'registersCarExit'])->name('cars.exit');

    Route::get('/employees/{parking}', [EmployeesController::class, 'index'])->name('employees.index');
    Route::get('/employees/{parking}/{id}', [EmployeesController::class, 'show'])->name('employees.show');
    Route::post('/employees', [EmployeesController::class, 'store'])->name('employees.store');
    Route::patch('/employees/{parking}/{id}', [EmployeesController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{parking}/{id}', [EmployeesController::class, 'destroy'])->name('employees.destroy');

    Route::get('/vacancies/{parking}', [VacanciesController::class, 'index'])->name('vacancies.index');
    Route::get('/vacancies/{parking}/{id}', [VacanciesController::class, 'show'])->name('vacancies.show');
    Route::post('/vacancies', [VacanciesController::class, 'store'])->name('vacancies.store');
    Route::patch('/vacancies/{parking}/{id}', [VacanciesController::class, 'update'])->name('vacancies.update');
    Route::delete('/vacancies/{parking}/{id}', [VacanciesController::class, 'destroy'])->name('vacancies.destroy');

    Route::get('/reservation/{parking}', [ReservationController::class, 'index'])->name('reservation.index');
    Route::get('/reservation/{parking}/{id}', [ReservationController::class, 'show'])->name('reservation.show');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
    Route::patch('/reservation/{parking}/{id}', [ReservationController::class, 'update'])->name('reservation.update');

});
