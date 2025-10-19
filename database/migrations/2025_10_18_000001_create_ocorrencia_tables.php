<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🧱 1️⃣ Modelo Motivo (motivos personalizáveis por escola)
        Schema::create(prefix('modelo_motivo'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('descricao', 255);
            $table->string('categoria', 100)->nullable();
            $table->timestamps();
        });

        // 🧱 2️⃣ Ocorrência (principal)
        Schema::create(prefix('ocorrencia'), function (Blueprint $table) {
            $table->id();

            // Contexto institucional
            $table->unsignedBigInteger('school_id');
            $table->integer('ano_letivo')->default(date('Y'));
            $table->boolean('vigente')->default(true);

            // Relacionamentos
            $table->unsignedBigInteger('aluno_id');
            $table->unsignedBigInteger('professor_id');
            $table->unsignedBigInteger('oferta_id')->nullable();

            // Informações principais
            $table->text('descricao')->nullable();
            $table->string('local', 100)->nullable();
            $table->string('atitude', 100)->nullable();
            $table->string('outra_atitude', 150)->nullable();
            $table->string('comportamento', 100)->nullable();
            $table->text('sugestao')->nullable();
            $table->tinyInteger('status')->default(1); // 1=ativa, 0=arquivada, 2=anulada
            $table->tinyInteger('nivel_gravidade')->default(1);
            $table->tinyInteger('sync')->default(1);
            $table->timestamp('recebido_em')->nullable();
            $table->text('encaminhamentos')->nullable();

            // Controle temporal
            $table->timestamps();

            // Índices e chaves
            $table->index(['school_id', 'aluno_id']);
            $table->index(['professor_id', 'oferta_id']);
        });

        // 🧱 3️⃣ Pivot Ocorrência ↔ Motivo
        Schema::create(prefix('ocorrencia_motivo'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ocorrencia_id');
            $table->unsignedBigInteger('modelo_motivo_id');
            $table->timestamps();

            $table->foreign('ocorrencia_id')
                  ->references('id')
                  ->on(prefix('ocorrencia'))
                  ->onDelete('cascade');

            $table->foreign('modelo_motivo_id')
                  ->references('id')
                  ->on(prefix('modelo_motivo'))
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('ocorrencia_motivo'));
        Schema::dropIfExists(prefix('ocorrencia'));
        Schema::dropIfExists(prefix('modelo_motivo'));
    }
};
