<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $fillable = [
        'campeonato_id',
        'primeiro_lugar_id',
        'segundo_lugar_id',
        'terceiro_lugar_id',
    ];

    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class);
    }

    public function primeiroLugar()
    {
        return $this->belongsTo(Time::class, 'primeiro_lugar_id');
    }

    public function segundoLugar()
    {
        return $this->belongsTo(Time::class, 'segundo_lugar_id');
    }

    public function terceiroLugar()
    {
        return $this->belongsTo(Time::class, 'terceiro_lugar_id');
    }
}