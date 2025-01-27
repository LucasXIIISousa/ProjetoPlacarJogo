<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'campeonato_id',
        'primeiro_lugar_id',
        'segundo_lugar_id',
        'terceiro_lugar_id',
    ];

    // Relacionamento com o campeonato
    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class);
    }

    // Relacionamento com o time que ficou em primeiro lugar
    public function primeiroLugar()
    {
        return $this->belongsTo(Time::class, 'primeiro_lugar_id');
    }

    // Relacionamento com o time que ficou em segundo lugar
    public function segundoLugar()
    {
        return $this->belongsTo(Time::class, 'segundo_lugar_id');
    }

    // Relacionamento com o time que ficou em terceiro lugar
    public function terceiroLugar()
    {
        return $this->belongsTo(Time::class, 'terceiro_lugar_id');
    }
}