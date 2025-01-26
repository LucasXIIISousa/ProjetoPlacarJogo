<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampeonatoController;
use App\Http\Controllers\TimeController;

// Rotas para Campeonato
Route::get('/campeonatos', [CampeonatoController::class, 'index']); // Listar todos os campeonatos
Route::post('/campeonatos', [CampeonatoController::class, 'store']); // Criar um novo campeonato

Route::post('/campeonatos/{id}/simular', [CampeonatoController::class, 'simular']); // Simular um campeonato

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

// Rotas para Time
Route::get('/times', [TimeController::class, 'index']); // Listar todos os times
Route::post('/times', [TimeController::class, 'store']); // Criar um novo time

Route::get('/test-session', function () {
    session(['test_key' => 'test_value']);
    return session('test_key');
});