<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('enturmacao'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('aluno_id');
            $table->unsignedBigInteger('turma_id');

            $table->foreign('aluno_id')
                ->references('id')
                ->on(prefix('aluno'))
                ->onDelete('cascade');

            $table->foreign('turma_id')
                ->references('id')
                ->on(prefix('turma'))
                ->onDelete('cascade');

            $table->foreign('school_id')
                ->references('id')
                ->on(prefix('escola'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('enturmacao'));
    }
};
