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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id('pes_id');
            $table->string('pes_nome',200)->index();
            $table->string('pes_cpf',14)->unique()->index();
            $table->date('pes_data_nascimento')->index();
            $table->string('pes_sexo', 9)->index();
            $table->string('pes_mae',200)->nullable();
            $table->string('pes_pai',200)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
