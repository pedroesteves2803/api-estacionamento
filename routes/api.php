<?php

use App\Http\Controllers\Carro\CarroController;
use App\Http\Controllers\Cars\CarsController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Estacionamento\EstacionamentoController;
use App\Http\Controllers\Funcionario\FuncionarioController;
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

Route::apiResource('/parking', ParkingController::class);

Route::apiResource('/car', CarsController::class);
Route::patch('/car/output/{car}', [CarsController::class, 'registersCarExit']);

Route::apiResource('/employees', EmployeesController::class);
