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
        Schema::create('pessoa_endereco', function (Blueprint $table) {
            $table->unsignedBigInteger('pes_id');
            $table->unsignedBigInteger('end_id');
            $table->timestamps();

            $table->primary(['pes_id', 'end_id']);

            $table->foreign('pes_id')->references('pes_id')->on('pessoas')->onDelete('cascade');
            $table->foreign('end_id')->references('end_id')->on('enderecos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoa_endereco');
    }
};
