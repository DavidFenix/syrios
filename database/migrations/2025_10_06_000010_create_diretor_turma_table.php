<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('diretor_turma'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professor_id');
            $table->unsignedBigInteger('turma_id');
            $table->unsignedBigInteger('school_id');
            $table->timestamps(); // ✅ adiciona created_at e updated_at

            $table->unique(['professor_id', 'turma_id'], 'uq_diretor_turma');

            $table->foreign('professor_id')->references('id')->on(prefix('professor'))->onDelete('cascade');
            $table->foreign('turma_id')->references('id')->on(prefix('turma'))->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on(prefix('escola'))->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('diretor_turma'));
    }
};
