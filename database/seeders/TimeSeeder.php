<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Time;

class TimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $times = [
            ['nome' => 'Time A'],
            ['nome' => 'Time B'],
            ['nome' => 'Time C'],
            ['nome' => 'Time D'],
            ['nome' => 'Time E'],
            ['nome' => 'Time F'],
            ['nome' => 'Time G'],
            ['nome' => 'Time H'],
        ];

        foreach ($times as $time) {
            Time::create($time);
        }
    }
}
