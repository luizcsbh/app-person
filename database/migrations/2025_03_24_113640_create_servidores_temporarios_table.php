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
        Schema::create('servidores_temporarios', function (Blueprint $table) {
            $table->id('st_id');
            $table->unsignedBigInteger('pes_id')->unique();
            $table->date('st_data_admissao');
            $table->date('st_data_demissao')->nullable();
            $table->timestamps();

            $table->foreign('pes_id')->references('pes_id')->on('pessoas')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servidores_temporarios');
    }
};
