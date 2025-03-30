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
        Schema::create('lotacoes', function (Blueprint $table) {
            $table->id('lot_id');
            $table->unsignedBigInteger('pes_id');   
            $table->unsignedBigInteger('unid_id');
            $table->date('lot_data_lotacao');
            $table->date('lot_data_remocao')->nullable(); 
            $table->string('lot_portaria', 100);
            $table->timestamps();

            $table->foreign('pes_id')->references('pes_id')->on('pessoas')->onDelete('cascade');
            $table->foreign('unid_id')->references('unid_id')->on('unidades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotacoes');
    }
};
