<?php

use App\Http\Controllers\PartidaController;

Route::get('/simular-placar', [PartidaController::class, 'simularPlacar']);

