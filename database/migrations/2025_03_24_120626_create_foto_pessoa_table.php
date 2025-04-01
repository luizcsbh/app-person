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
        Schema::create('foto_pessoa', function (Blueprint $table) {
            $table->id('fp_id');
            $table->unsignedBigInteger('pes_id');
            $table->date('fp_data')->index();
            $table->string('fp_arquivo');
            $table->string('fp_bucket', 50);
            $table->string('fp_hash', 64)->unique();
            $table->timestamps();

            $table->foreign('pes_id')->references('pes_id')->on('pessoas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_pessoa');
    }
};
