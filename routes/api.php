<?php

use App\Http\Controllers\GastoController;
use App\Http\Controllers\IngresoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

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

// Rutas públicas (no requieren autenticación)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    // Rutas de ingresos
    Route::get('/ingresos', [IngresoController::class, 'index']);
    Route::post('/ingresos', [IngresoController::class, 'store']);
    Route::get('/ingresos/{id}', [IngresoController::class, 'show']);
    Route::put('/ingresos/{id}', [IngresoController::class, 'update']);
    Route::delete('/ingresos/{id}', [IngresoController::class, 'destroy']);

    // Rutas de gastos
    Route::post('/gastos', [GastoController::class, 'store']);
    Route::get('/gastos', [GastoController::class, 'index']);
    Route::delete('/gastos/{id}', [GastoController::class, 'destroy']);
    Route::put('/gastos/{id}', [GastoController::class, 'update']);
    Route::get('/gastos/{id}', [GastoController::class, 'show']);

    // Otras rutas protegidas
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
