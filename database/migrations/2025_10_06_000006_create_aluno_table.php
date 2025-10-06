<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('aluno'), function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 10);
            $table->unsignedBigInteger('school_id');
            $table->string('nome_a', 100);

            $table->foreign('school_id')
                ->references('id')
                ->on(prefix('escola'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('aluno'));
    }
};
