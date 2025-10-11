<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('oferta'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('turma_id');
            $table->unsignedBigInteger('disciplina_id');
            $table->unsignedBigInteger('professor_id');
            $table->tinyInteger('status');
            $table->timestamps(); // ✅ adiciona created_at e updated_at

            $table->foreign('school_id')->references('id')->on(prefix('escola'))->onDelete('cascade');
            $table->foreign('turma_id')->references('id')->on(prefix('turma'))->onDelete('cascade');
            $table->foreign('disciplina_id')->references('id')->on(prefix('disciplina'))->onDelete('cascade');
            $table->foreign('professor_id')->references('id')->on(prefix('professor'))->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('oferta'));
    }
};
