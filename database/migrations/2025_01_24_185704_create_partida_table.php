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
            $table->unsignedBigInteger('time1_id');
            $table->unsignedBigInteger('time2_id');
            $table->integer('golsTime1')->default(0);
            $table->integer('golsTime2')->default(0); 
            $table->string('fase', 50); // Ex.: "quartas", "semifinal", "final"
            $table->timestamps();
            $table->foreign('time1_id')->references('id')->on('tabTimes')->onDelete('cascade');
            $table->foreign('time2_id')->references('id')->on('tabTimes')->onDelete('cascade');
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
