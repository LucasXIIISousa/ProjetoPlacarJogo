<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campeonato extends Model
{
    protected $fillable = ['nome'];

    // Relacionamento com Partida (um campeonato tem muitas partidas)
    public function partidas()
    {
        return $this->hasMany(Partida::class);
    }

    // Relacionamento com Resultado (um campeonato tem um resultado)
    public function resultado()
    {
        return $this->hasOne(Resultado::class);
    }
}