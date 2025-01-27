<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $fillable = ['nome', 'pontuacao'];

    public function partidasComoTime1()
    {
        return $this->hasMany(Partida::class, 'time1_id');
    }

    public function partidasComoTime2()
    {
        return $this->hasMany(Partida::class, 'time2_id');
    }
}