<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Time; 

class TimeController extends Controller
{

    public function index()
    {
        return Time::all();
    }

    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|string|max:255']);
    
        if (Time::count() >= 8) {
            return response()->json(['error' => 'O número máximo de times (8) já foi atingido.'], 400);
        }
    
        return Time::create($request->all());
    }


    public function destroy($id)
    {

        $time = Time::find($id);

        if (!$time) {
            return response()->json(['error' => 'Time não encontrado'], 404);
        }

        $time->delete();

        return response()->json(['message' => 'Time deletado com sucesso!'], 200);
    }

    public function atualizar(Request $request, $id)
    {
        // Validação dos dados recebidos
        $request->validate([
            'nome' => 'required|string',
            'pontuacao' => 'required|integer',
        ]);

        $time = Time::findOrFail($id);

        $time->nome = $request->nome;
        $time->pontuacao = $request->pontuacao;
        $time->save(); 

        return response()->json($time, 200);
    }

    public function atualizarParcial(Request $request, $id)
    {

        $request->validate([
            'nome' => 'string', 
            'pontuacao' => 'integer',
        ]);

        $time = Time::findOrFail($id);

        if ($request->has('nome')) {
            $time->nome = $request->nome;
        }

        if ($request->has('pontuacao')) {
            $time->pontuacao = $request->pontuacao;
        }

        $time->save();

        return response()->json($time, 200);
    }
}