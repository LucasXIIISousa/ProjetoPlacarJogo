<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campeonato;
use App\Models\Resultado;

class ResultadoController extends Controller
{
    public function updateResultado(Request $request, $id)
    {
        $campeonato = Campeonato::findOrFail($id);
        $resultado = $campeonato->resultado;

        if (!$resultado) {
            return response()->json(['error' => 'Resultado not found'], 404);
        }

        $validatedData = $request->validate([
            'primeiroLugar' => 'nullable|exists:times,id',
            'segundoLugar' => 'nullable|exists:times,id',
            'terceiroLugar' => 'nullable|exists:times,id',
        ]);

        $resultado->update($validatedData);

        return response()->json(['message' => 'Resultado updated successfully', 'resultado' => $resultado], 200);
    }

    public function patchResultado(Request $request, $id)
    {
        $campeonato = Campeonato::findOrFail($id);
        $resultado = $campeonato->resultado;

        if (!$resultado) {
            return response()->json(['error' => 'Resultado not found'], 404);
        }

        $validatedData = $request->validate([
            'primeiroLugar' => 'nullable|exists:times,id',
            'segundoLugar' => 'nullable|exists:times,id',
            'terceiroLugar' => 'nullable|exists:times,id',
        ]);

        foreach ($validatedData as $key => $value) {
            if ($value !== null) {
                $resultado->$key = $value;
            }
        }

        $resultado->save();

        return response()->json(['message' => 'Resultado partially updated successfully', 'resultado' => $resultado], 200);
    }

    public function deleteResultado($id)
    {
        $campeonato = Campeonato::findOrFail($id);
        $resultado = $campeonato->resultado;

        if (!$resultado) {
            return response()->json(['error' => 'Resultado not found'], 404);
        }

        $resultado->delete();

        return response()->json(['message' => 'Resultado deleted successfully'], 200);
    }
}
