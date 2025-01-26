<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $fillable = ['campeonato_id', 'time_casa_id', 'time_visitante_id', 'gols_casa', 'gols_visitante', 'fase', 'vencedor_id'];

    public function timeCasa()
    {
        return $this->belongsTo(Time::class, 'time_casa_id');
    }

    public function timeVisitante()
    {
        return $this->belongsTo(Time::class, 'time_visitante_id');
    }

    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class);
    }
}