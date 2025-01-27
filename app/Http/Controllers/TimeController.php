<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Time; // Importe o modelo Time

class TimeController extends Controller
{
    // Listar todos os times
    public function index()
    {
        return Time::all();
    }

    // Criar um novo time
    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|string|max:255']);
        return Time::create($request->all());
    }

    // Deletar um time
    public function destroy($id)
    {
        // Encontra o time pelo ID
        $time = Time::find($id);

        // Verifica se o time existe
        if (!$time) {
            return response()->json(['error' => 'Time não encontrado'], 404);
        }

        // Deleta o time
        $time->delete();

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Time deletado com sucesso!'], 200);
    }
}