<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('turma'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('serie_turma', 20);
            $table->string('turno', 20);
            $table->timestamps(); // ✅ adiciona created_at e updated_at

            $table->foreign('school_id')
                ->references('id')
                ->on(prefix('escola'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('turma'));
    }
};
