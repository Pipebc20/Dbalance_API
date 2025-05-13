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
| Rutas API para la aplicación DBalance
| Todas las rutas aquí definidas serán prefijadas con /api
|
*/

// 1. Rutas Públicas (sin autenticación)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']); // Nueva ruta para restablecimiento

// 2. Rutas Protegidas (requieren autenticación Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // 2.1 Rutas de Ingresos (CRUD completo)
    Route::get('/ingresos', [IngresoController::class, 'index']);
    Route::post('/ingresos', [IngresoController::class, 'store']);
    Route::get('/ingresos/{id}', [IngresoController::class, 'show']);
    Route::put('/ingresos/{id}', [IngresoController::class, 'update']);
    Route::delete('/ingresos/{id}', [IngresoController::class, 'destroy']);

    // 2.2 Rutas de Gastos (CRUD completo)
    Route::get('/gastos', [GastoController::class, 'index']);
    Route::post('/gastos', [GastoController::class, 'store']);
    Route::get('/gastos/{id}', [GastoController::class, 'show']);
    Route::put('/gastos/{id}', [GastoController::class, 'update']);
    Route::delete('/gastos/{id}', [GastoController::class, 'destroy']);

    // 2.3 Rutas de Autenticación y Usuario
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
