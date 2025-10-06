<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('usuario'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('cpf', 11);
            $table->string('senha_hash', 255);
            $table->string('nome_u', 100);
            $table->tinyInteger('status');
            $table->boolean('is_super_master')->default(0);

            $table->unique(['cpf', 'school_id'], 'uq_usuario_cpf_escola');

            $table->foreign('school_id')
                ->references('id')
                ->on(prefix('escola'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('usuario'));
    }
};
