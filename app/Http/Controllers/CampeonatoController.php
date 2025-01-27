<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campeonato;
use App\Models\Time;
use App\Models\Partida;
use App\Models\Resultado;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
    
        // Verifica se há exatamente 8 times no campeonato
        $times = Time::all();
        if ($times->count() != 8) {
            return response()->json(['error' => 'O campeonato deve ter exatamente 8 times'], 400);
        }
    
        // Chaveamento das partidas
        $quartas = $this->chaveamentoQuartas($times);
        $semifinais = $this->chaveamentoSemifinais($quartas);
        $final = $this->chaveamentoFinal($semifinais);
    
        // Simulação das partidas
        $resultados = $this->simularPartidas($quartas, $semifinais, $final);
    
        // Cria um novo resultado
        $resultado = Resultado::create([
            'campeonato_id' => $campeonato->id,
            'primeiro_lugar_id' => $resultados['vencedor']->id,
            'segundo_lugar_id' => $resultados['segundo']->id,
            'terceiro_lugar_id' => $resultados['terceiro']->id,
        ]);
    
        // Atualiza o campeonato com o resultado_id
        $campeonato->update([
            'resultado_id' => $resultado->id,
        ]);
    
        // Retorna o resultado da simulação
        return response()->json([
            'message' => 'Campeonato simulado com sucesso!',
            'resultado' => $resultados,
            'campeonato' => $campeonato,
        ]);
    }

    // Método para simular placar usando o script Python
    private function simularPlacar()
    {
        // Caminho completo para o script Python
        $caminhoScript = base_path('resources/python/teste.py');
    
        // Executa o script Python
        $process = new Process(['py', $caminhoScript]);
        $process->run();
    
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    
        $output = $process->getOutput();
        $placar = explode("\n", trim($output));
    
        return [
            'gols_time1' => (int)$placar[0],
            'gols_time2' => (int)$placar[1],
        ];
    }

    // Método para chaveamento das quartas de final
    private function chaveamentoQuartas($times)
    {
        $times = $times->shuffle(); // Embaralha os times
        $quartas = [];

        for ($i = 0; $i < 4; $i++) {
            $quartas[] = [
                'time1' => $times->pop(),
                'time2' => $times->pop(),
            ];
        }

        return $quartas;
    }

    // Método para chaveamento das semifinais
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

    // Método para chaveamento da final e disputa do 3º lugar
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

    // Método para calcular o vencedor de uma partida
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
            // Empate: desempate por pontuação
            if ($time1->pontuacao > $time2->pontuacao) {
                return $time1;
            } elseif ($time1->pontuacao < $time2->pontuacao) {
                return $time2;
            } else {
                // Empate na pontuação: desempate por ordem de inscrição
                return $time1->created_at < $time2->created_at ? $time1 : $time2;
            }
        }
    }

    // Método para simular todas as partidas
    private function simularPartidas($quartas, $semifinais, $final)
    {
        // Simula a final
        $placarFinal = $this->simularPlacar();
        $vencedorFinal = $this->calcularVencedor($final['final']['time1'], $final['final']['time2'], $placarFinal);
        $segundoLugar = $final['final']['time1']->id == $vencedorFinal->id ? $final['final']['time2'] : $final['final']['time1'];

        // Simula a disputa do 3º lugar
        $placarTerceiro = $this->simularPlacar();
        $terceiroLugar = $this->calcularVencedor($final['terceiroLugar']['time1'], $final['terceiroLugar']['time2'], $placarTerceiro);

        return [
            'vencedor' => $vencedorFinal,
            'segundo' => $segundoLugar,
            'terceiro' => $terceiroLugar,
        ];
    }

    public function resultados($id)
    {
        // Encontra o campeonato pelo ID com os relacionamentos
        $campeonato = Campeonato::with(['resultado.primeiroLugar', 'resultado.segundoLugar', 'resultado.terceiroLugar'])->find($id);
    
        // Verifica se o campeonato existe
        if (!$campeonato) {
            return response()->json(['error' => 'Campeonato não encontrado'], 404);
        }
    
        // Verifica se o resultado existe
        if (!$campeonato->resultado) {
            return response()->json([
                'campeonato' => [
                    'id' => $campeonato->id,
                    'nome' => $campeonato->nome,
                    'resultado_id' => $campeonato->resultado_id,
                    'created_at' => $campeonato->created_at,
                    'updated_at' => $campeonato->updated_at,
                    'resultado' => null, // Resultado não existe
                ],
            ]);
        }
    
        // Formata a resposta para incluir apenas os times
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
}