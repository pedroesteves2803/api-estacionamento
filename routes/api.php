<?php

use App\Http\Controllers\Estacaionamento\EstacionamentoController;
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

Route::get('/estacionamento', [EstacionamentoController::class, 'index']);
Route::post('/estacionamento', [EstacionamentoController::class, 'store']);
