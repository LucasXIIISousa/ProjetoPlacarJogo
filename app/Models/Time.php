<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

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