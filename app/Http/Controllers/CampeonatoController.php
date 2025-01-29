<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campeonato;
use App\Models\Time;
use App\Models\Partida;
use App\Models\Resultado;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

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

    public function simular($id)
    {
        $campeonato = Campeonato::find($id);
    
        if (!$campeonato) {
            return response()->json(['error' => 'Campeonato não encontrado'], 404);
        }
    
        $times = Time::all();
        if ($times->count() != 8) {
            return response()->json(['error' => 'O campeonato deve ter exatamente 8 times'], 400);
        }
    
        $quartas = $this->chaveamentoQuartas($times);
        $semifinais = $this->chaveamentoSemifinais($quartas);
        $final = $this->chaveamentoFinal($semifinais);
    
        $resultados = $this->simularPartidas($quartas, $semifinais, $final, $campeonato);
    
        $resultado = Resultado::create([
            'campeonato_id' => $campeonato->id,
            'primeiro_lugar_id' => $resultados['vencedor']->id,
            'segundo_lugar_id' => $resultados['segundo']->id,
            'terceiro_lugar_id' => $resultados['terceiro']->id,
        ]);
    
        $campeonato->update([
            'resultado_id' => $resultado->id,
        ]);

        return response()->json([
            'message' => 'Campeonato simulado com sucesso!',
            'resultado' => $resultados,
            'campeonato' => $campeonato,
        ]);
    }

    private function simularPlacar()
    {
        $caminhoScript = base_path('resources/python/teste.py');
    
        $process = new Process(['py', $caminhoScript]);
        $process->run();
    
        if (!$process->isSuccessful()) {
            Log::error('Erro ao executar script Python: ' . $process->getErrorOutput());
            throw new ProcessFailedException($process);
        }
    
        $output = $process->getOutput();
        $placar = explode("\n", trim($output));
    
        Log::info('Placar simulado: ' . json_encode($placar));
    
        return [
            'gols_time1' => (int)$placar[0],
            'gols_time2' => (int)$placar[1],
        ];
    }

    private function chaveamentoQuartas($times)
    {
        $times = $times->shuffle();
        $quartas = [];

        for ($i = 0; $i < 4; $i++) {
            $quartas[] = [
                'time1' => $times->pop(),
                'time2' => $times->pop(),
            ];
        }

        return $quartas;
    }

    private function chaveamentoSemifinais($quartas)
    {
        $vencedores = [];

        foreach ($quartas as $partida) {
            $placar = $this->simularPlacar();
            $vencedor = $this->calcularVencedor($partida['time1'], $partida['time2'], $placar);
            $vencedores[] = $vencedor;
        }

        return [
            [
                'time1' => $vencedores[0],
                'time2' => $vencedores[1],
            ],
            [
                'time1' => $vencedores[2],
                'time2' => $vencedores[3],
            ],
        ];
    }

    private function chaveamentoFinal($semifinais)
    {
        $finalistas = [];
        $terceiroLugar = [];

        foreach ($semifinais as $partida) {
            $placar = $this->simularPlacar();
            $vencedor = $this->calcularVencedor($partida['time1'], $partida['time2'], $placar);
            $finalistas[] = $vencedor;
            $terceiroLugar[] = $partida['time1']->id == $vencedor->id ? $partida['time2'] : $partida['time1'];
        }

        return [
            'final' => [
                'time1' => $finalistas[0],
                'time2' => $finalistas[1],
            ],
            'terceiroLugar' => [
                'time1' => $terceiroLugar[0],
                'time2' => $terceiroLugar[1],
            ],
        ];
    }

    private function calcularVencedor($time1, $time2, $placar)
    {
        $time1->pontuacao += $placar['gols_time1'] - $placar['gols_time2'];
        $time2->pontuacao += $placar['gols_time2'] - $placar['gols_time1'];
    
        $time1->save();
        $time2->save();
    
        if ($placar['gols_time1'] > $placar['gols_time2']) {
            return $time1;
        } elseif ($placar['gols_time1'] < $placar['gols_time2']) {
            return $time2;
        } else {
            return $this->simularPenaltis($time1, $time2);
        }
    }

    private function simularPartidas($quartas, $semifinais, $final, $campeonato)
    {
        foreach ($quartas as $partida) {
            $placar = $this->simularPlacar();
            $vencedor = $this->calcularVencedor($partida['time1'], $partida['time2'], $placar);

            Partida::create([
                'campeonato_id' => $campeonato->id,
                'time1_id' => $partida['time1']->id,
                'time2_id' => $partida['time2']->id,
                'gols_time1' => $placar['gols_time1'],
                'gols_time2' => $placar['gols_time2'],
                'fase' => 'quartas',
                'vencedor_id' => $vencedor->id,
                'houve_penaltis' => $placar['gols_time1'] == $placar['gols_time2'],
            ]);
        }

        foreach ($semifinais as $partida) {
            $placar = $this->simularPlacar();
            $vencedor = $this->calcularVencedor($partida['time1'], $partida['time2'], $placar);

            Partida::create([
                'campeonato_id' => $campeonato->id,
                'time1_id' => $partida['time1']->id,
                'time2_id' => $partida['time2']->id,
                'gols_time1' => $placar['gols_time1'],
                'gols_time2' => $placar['gols_time2'],
                'fase' => 'semifinais',
                'vencedor_id' => $vencedor->id,
            ]);
        }

        $placarFinal = $this->simularPlacar();
        $vencedorFinal = $this->calcularVencedor($final['final']['time1'], $final['final']['time2'], $placarFinal);
        $segundoLugar = $final['final']['time1']->id == $vencedorFinal->id ? $final['final']['time2'] : $final['final']['time1'];

        Partida::create([
            'campeonato_id' => $campeonato->id,
            'time1_id' => $final['final']['time1']->id,
            'time2_id' => $final['final']['time2']->id,
            'gols_time1' => $placarFinal['gols_time1'],
            'gols_time2' => $placarFinal['gols_time2'],
            'fase' => 'final',
            'vencedor_id' => $vencedorFinal->id,
        ]);

        $placarTerceiro = $this->simularPlacar();
        $terceiroLugar = $this->calcularVencedor($final['terceiroLugar']['time1'], $final['terceiroLugar']['time2'], $placarTerceiro);

        Partida::create([
            'campeonato_id' => $campeonato->id,
            'time1_id' => $final['terceiroLugar']['time1']->id,
            'time2_id' => $final['terceiroLugar']['time2']->id,
            'gols_time1' => $placarTerceiro['gols_time1'],
            'gols_time2' => $placarTerceiro['gols_time2'],
            'fase' => 'disputa_terceiro',
            'vencedor_id' => $terceiroLugar->id,
        ]);

        return [
            'vencedor' => $vencedorFinal,
            'segundo' => $segundoLugar,
            'terceiro' => $terceiroLugar,
        ];
    }

    public function resultados($id)
    {
        $campeonato = Campeonato::with(['resultado.primeiroLugar', 'resultado.segundoLugar', 'resultado.terceiroLugar'])->find($id);
    
        if (!$campeonato) {
            return response()->json(['error' => 'Campeonato não encontrado'], 404);
        }
    
        if (!$campeonato->resultado) {
            return response()->json([
                'campeonato' => [
                    'id' => $campeonato->id,
                    'nome' => $campeonato->nome,
                    'resultado_id' => $campeonato->resultado_id,
                    'created_at' => $campeonato->created_at,
                    'updated_at' => $campeonato->updated_at,
                    'resultado' => null, 
                ],
            ]);
        }
    
        return response()->json([
            'campeonato' => [
                'id' => $campeonato->id,
                'nome' => $campeonato->nome,
                'resultado_id' => $campeonato->resultado_id,
                'created_at' => $campeonato->created_at,
                'updated_at' => $campeonato->updated_at,
                'resultado' => [
                    'primeiroLugar' => $campeonato->resultado->primeiroLugar,
                    'segundoLugar' => $campeonato->resultado->segundoLugar,
                    'terceiroLugar' => $campeonato->resultado->terceiroLugar,
                ],
            ],
        ]);
    }

    private function simularPenaltis($time1, $time2)
    {
        $penaltisTime1 = 0;
        $penaltisTime2 = 0;

        for ($i = 0; $i < 5; $i++) {
            $penaltisTime1 += rand(0, 1);
            $penaltisTime2 += rand(0, 1);
        }

        while ($penaltisTime1 == $penaltisTime2) {
            $penaltisTime1 += rand(0, 1);
            $penaltisTime2 += rand(0, 1);
        }

        return $penaltisTime1 > $penaltisTime2 ? $time1 : $time2;
    }

    public function destroy($id)
    {

        $campeonato = Campeonato::find($id);

        if (!$campeonato) {
            return response()->json(['error' => 'campeonato não encontrado'], 404);
        }

        $campeonato->delete();

        return response()->json(['message' => 'campeonato deletado com sucesso!'], 200);
    }

    public function atualizar(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date',
        ]);

        $campeonato = Campeonato::findOrFail($id);

        $campeonato->nome = $request->nome;
        $campeonato->data_inicio = $request->data_inicio;
        $campeonato->data_fim = $request->data_fim;
        $campeonato->save();

        return response()->json($campeonato, 200);
    }

    public function atualizarParcial(Request $request, $id)
    {
        $request->validate([
            'nome' => 'string', 
            'data_inicio' => 'date',
            'data_fim' => 'date',
        ]);

        $campeonato = Campeonato::findOrFail($id);

        if ($request->has('nome')) {
            $campeonato->nome = $request->nome;
        }

        if ($request->has('data_inicio')) {
            $campeonato->data_inicio = $request->data_inicio;
        }

        if ($request->has('data_fim')) {
            $campeonato->data_fim = $request->data_fim;
        }

        $campeonato->save();

        return response()->json($campeonato, 200);
    }
}