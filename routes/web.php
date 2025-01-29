<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampeonatoController;
use App\Http\Controllers\TimeController;

// Rotas para Campeonato
Route::get('/campeonatos', [CampeonatoController::class, 'index']); 
Route::post('/campeonatos', [CampeonatoController::class, 'store']); 
Route::delete('/campeonatos/{id}', [CampeonatoController::class, 'destroy']);
Route::put('/campeonatos/{id}', [CampeonatoController::class, 'atualizar']);
Route::patch('/campeonatos/{id}', [CampeonatoController::class, 'atualizarParcial']); 
Route::post('/campeonatos/{id}/simular', [CampeonatoController::class, 'simular']); 
Route::post('/campeonatos/{id}/simular-penaltis', [CampeonatoController::class, 'simularPenaltis']);
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});



// Rotas para Time
Route::get('/times', [TimeController::class, 'index']); 
Route::post('/times', [TimeController::class, 'store']); 
Route::delete('/times/{id}', [TimeController::class, 'destroy']);
Route::put('/times/{id}', [TimeController::class, 'atualizar']); 
Route::patch('/times/{id}', [TimeController::class, 'atualizarParcial']); 
Route::get('/test-session', function () {
    session(['test_key' => 'test_value']);
    return session('test_key');
});

// Rotas para ResultadoController
Route::get('/campeonatos/{id}/resultados', [CampeonatoController::class, 'resultados']);
Route::put('/campeonatos/{id}/resultados', [ResultadoController::class, 'updateResultado']);
Route::patch('/campeonatos/{id}/resultados', [ResultadoController::class, 'patchResultado']);
Route::delete('/campeonatos/{id}/resultados', [ResultadoController::class, 'deleteResultado']);