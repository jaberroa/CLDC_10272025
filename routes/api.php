<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MiembrosApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\CarnetController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Dashboard API Routes
Route::prefix('dashboard')->group(function () {
    Route::get('/estadisticas', [DashboardApiController::class, 'estadisticas']);
    Route::get('/miembros-por-tipo', [DashboardApiController::class, 'miembrosPorTipo']);
    Route::get('/organizaciones-por-tipo', [DashboardApiController::class, 'organizacionesPorTipo']);
    Route::get('/transacciones-recientes', [DashboardApiController::class, 'transaccionesRecientes']);
    Route::get('/asambleas-proximas', [DashboardApiController::class, 'asambleasProximas']);
    Route::get('/elecciones-proximas', [DashboardApiController::class, 'eleccionesProximas']);
    Route::get('/miembros-activos', [DashboardApiController::class, 'miembrosActivos']);
    Route::get('/resumen-financiero', [DashboardApiController::class, 'resumenFinanciero']);
});

// Miembros API Routes
Route::prefix('miembros')->group(function () {
    Route::get('/', [MiembrosApiController::class, 'index']);
    Route::get('/{id}', [MiembrosApiController::class, 'show']);
    Route::get('/estadisticas/estadisticas', [MiembrosApiController::class, 'estadisticas']);
    Route::get('/search/search', [MiembrosApiController::class, 'search']);
    Route::get('/filtros/organizaciones', [MiembrosApiController::class, 'organizaciones']);
    Route::get('/filtros/estados-membresia', [MiembrosApiController::class, 'estadosMembresia']);
});

// Carnet API Routes
Route::prefix('carnet')->group(function () {
    Route::get('/templates', [CarnetController::class, 'getTemplates']);
    Route::get('/templates/{template}', [CarnetController::class, 'getTemplate']);
    Route::get('/personalizacion/{miembro}/{template}', [CarnetController::class, 'getPersonalizacion']);
    Route::get('/miembro-data/{miembro}', [CarnetController::class, 'getMiembroData']);
});
