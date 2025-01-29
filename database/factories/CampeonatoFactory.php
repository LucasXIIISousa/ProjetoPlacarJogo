<?php

namespace Database\Factories;

use App\Models\Campeonato;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampeonatoFactory extends Factory
{
    protected $model = Campeonato::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->word, // Exemplo de dado faker
            'resultado_id' => null, // Ou defina um valor padrão
        ];
    }
}
