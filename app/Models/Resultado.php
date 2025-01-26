<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $fillable = ['campeonato_id', 'primeiro_lugar_id', 'segundo_lugar_id', 'terceiro_lugar_id'];

    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class);
    }
}