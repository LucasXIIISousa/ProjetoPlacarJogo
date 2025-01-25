<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    use HasFactory;

    // Nome da tabela correspondente
    protected $table = 'tabPartidas';

    // Colunas que podem ser preenchidas diretamente
    protected $fillable = [
        'time1_id',
        'time2_id',
        'gols_time1',
        'gols_time2',
    ];

    // Relacionamento com o Model Time (exemplo)
    public function time1()
    {
        return $this->belongsTo(Time::class, 'time1_id');
    }

    public function time2()
    {
        return $this->belongsTo(Time::class, 'time2_id');
    }
}
