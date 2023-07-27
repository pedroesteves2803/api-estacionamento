<?php

use App\Http\Controllers\Carro\CarroController;
use App\Http\Controllers\Estacionamento\EstacionamentoController;
use Illuminate\Http\Request;
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

Route::apiResource('/estacionamento', EstacionamentoController::class);
Route::apiResource('/carro', CarroController::class);

// Route::get('/estacionamento/{id}', [EstacionamentoController::class, 'show']);
// Route::get('/estacionamento', [EstacionamentoController::class, 'index']);
// Route::post('/estacionamento', [EstacionamentoController::class, 'store']);
// Route::patch('/estacionamento/{id}', [EstacionamentoController::class, 'update']);
// Route::delete('estacionamento/{id}', [EstacionamentoController::class, 'destroy']);
