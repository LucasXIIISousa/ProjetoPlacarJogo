<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $fillable = ['campeonato_id', 'time1_id', 'time2_id', 'gols_time1', 'gols_time2', 'fase', 'vencedor_id'];

    public function time1()
    {
        return $this->belongsTo(Time::class, 'time1_id');
    }

    public function time2()
    {
        return $this->belongsTo(Time::class, 'time2_id');
    }

    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class);
    }
}