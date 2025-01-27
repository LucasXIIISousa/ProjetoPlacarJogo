<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campeonato extends Model
{
    protected $fillable = ['nome', 'resultado_id'];

    public function resultado()
    {
        return $this->hasOne(Resultado::class);
    }

    // Adicione este método para definir o relacionamento com as partidas
    public function partidas()
    {
        return $this->hasMany(Partida::class);
    }
}