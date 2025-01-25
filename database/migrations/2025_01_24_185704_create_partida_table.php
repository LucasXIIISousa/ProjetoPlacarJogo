<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tabPartidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time1_id')->constrained('tabTimes')->onDelete('cascade');
            $table->foreignId('time2_id')->constrained('tabTimes')->onDelete('cascade');
            $table->integer('gols_time1')->default(0);
            $table->integer('gols_time2')->default(0);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabTimes');
    }
};
