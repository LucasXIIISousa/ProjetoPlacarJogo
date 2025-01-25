<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

    // Definindo a tabela associada ao model
    protected $table = 'tabTimes';

    // Permitir que as colunas abaixo sejam preenchidas automaticamente
    protected $fillable = [
        'nome',
        'pontuacao',
        'vitorias',
        'derrotas',
        'empates',
        'golsMarcados',
        'golsSofridos',
    ];

    // Relacionamento com os jogos (times podem participar de vários jogos)
    public function jogosComoTime1()
    {
        return $this->hasMany(Jogo::class, 'time1_id');
    }

    public function jogosComoTime2()
    {
        return $this->hasMany(Jogo::class, 'time2_id');
    }
}
