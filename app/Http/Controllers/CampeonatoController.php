<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campeonato; // Importe o modelo Campeonato

class CampeonatoController extends Controller
{
    // Listar todos os campeonatos
    public function index()
    {
        return Campeonato::with('partidas', 'resultado')->get();
    }

    // Criar um novo campeonato
    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|string|max:255']);
        return Campeonato::create($request->all());
    }

    // Simular um campeonato
    public function simular($id)
    {
        // Encontra o campeonato pelo ID
        $campeonato = Campeonato::find($id);
    
        // Verifica se o campeonato existe
        if (!$campeonato) {
            return response()->json(['error' => 'Campeonato não encontrado'], 404);
        }
    
        // Simulação do campeonato
        $resultadoSimulacao = [
            'vencedor' => 'Time A',
            'segundo_lugar' => 'Time B',
            'terceiro_lugar' => 'Time C',
        ];
    
        // Atualiza o campeonato com o resultado da simulação
        $campeonato->update([
            'vencedor_id' => 1, // ID do time vencedor
            'segundo_lugar_id' => 2, // ID do segundo colocado
            'terceiro_lugar_id' => 3, // ID do terceiro colocado
        ]);
    
        // Retorna o resultado da simulação
        return response()->json([
            'message' => 'Campeonato simulado com sucesso!',
            'resultado' => $resultadoSimulacao,
            'campeonato' => $campeonato,
        ]);
    }
}