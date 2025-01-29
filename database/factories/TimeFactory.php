<?php

namespace Database\Factories;

use App\Models\Time;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeFactory extends Factory
{
    protected $model = Time::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->word,
            'pontuacao' => 0,
        ];
    }
}