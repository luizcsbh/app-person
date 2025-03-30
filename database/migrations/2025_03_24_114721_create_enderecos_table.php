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
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id('end_id');
            $table->unsignedBigInteger('cid_id');
            $table->string('end_tipo_logradouro', 50)->index();
            $table->string('end_logradouro', 200)->index();
            $table->string('end_numero', 20)->index();
            $table->string('end_complemento',100)->nullable();
            $table->string('end_bairro', 100)->index();
            $table->timestamps();
            
            $table->foreign('cid_id')->references('cid_id')->on('cidades')->onDelete('cascade');
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
