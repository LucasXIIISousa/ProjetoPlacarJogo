<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $fillable = ['nome', 'pontuacao'];

    public function partidasComoCasa()
    {
        return $this->hasMany(Partida::class, 'time_casa_id');
    }

    public function partidasComoVisitante()
    {
        return $this->hasMany(Partida::class, 'time_visitante_id');
    }
}
