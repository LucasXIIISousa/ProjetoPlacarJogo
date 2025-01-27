<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campeonato_id')->constrained()->onDelete('cascade');
            $table->foreignId('time1_id')->constrained('times'); // Alterado para time1_id
            $table->foreignId('time2_id')->constrained('times'); // Alterado para time2_id
            $table->integer('gols_time1'); // Alterado para gols_time1
            $table->integer('gols_time2'); // Alterado para gols_time2
            $table->enum('fase', ['quartas', 'semifinais', 'final', 'disputa_terceiro']);
            $table->foreignId('vencedor_id')->nullable()->constrained('times');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};