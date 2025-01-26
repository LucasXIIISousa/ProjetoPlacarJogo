<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campeonato_id')->constrained()->onDelete('cascade');
            $table->foreignId('time_casa_id')->constrained('times');
            $table->foreignId('time_visitante_id')->constrained('times');
            $table->integer('gols_casa');
            $table->integer('gols_visitante');
            $table->enum('fase', ['quartas', 'semifinais', 'final', 'disputa_terceiro']);
            $table->foreignId('vencedor_id')->nullable()->constrained('times');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
