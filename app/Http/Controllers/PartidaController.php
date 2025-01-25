<?php

namespace App\Http\Controllers;

use App\Models\Partida;

class PartidaController extends Controller
{
    public function simularPlacar()
    {
        $output = shell_exec('py ' . base_path('resources/python/teste.py'));

        if (!$output) {
            return response()->json(['error' => 'Erro ao executar o script Python.'], 500);
        }

        $placar = explode("\n", trim($output));

        if (count($placar) < 2) {
            return response()->json(['error' => 'Saída inesperada do script Python.'], 500);
        }

        $gols_time1 = $placar[0];
        $gols_time2 = $placar[1];

        // Criar a partida e salvar no banco
        $partida = new Partida();
        $partida->time1_id = 1; // Substituir pelos IDs reais
        $partida->time2_id = 2;
        $partida->gols_time1 = $gols_time1;
        $partida->gols_time2 = $gols_time2;
        $partida->save();

        return response()->json(['success' => 'Placar salvo com sucesso!', 'partida' => $partida]);
    }
}
