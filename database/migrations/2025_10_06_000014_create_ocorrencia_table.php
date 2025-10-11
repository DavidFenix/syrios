<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(prefix('ocorrencia'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('professor_id');
            $table->unsignedBigInteger('aluno_id');
            $table->unsignedBigInteger('oferta_id');
            $table->unsignedBigInteger('modelo_motivo_id')->nullable();
            $table->unsignedBigInteger('status_id')->default(1);
            $table->dateTime('data_ocorrencia')->useCurrent();
            $table->text('descricao');
            $table->string('local', 100)->nullable();
            $table->string('atitude', 100)->nullable();
            $table->text('outra_acoes')->nullable();
            $table->string('comportamento', 100)->nullable();
            $table->text('medidas')->nullable();
            $table->text('encaminhamento')->nullable();
            $table->dateTime('recebido_em')->nullable();
            $table->boolean('sync')->default(0);
            //$table->timestamp('criado_em')->useCurrent();
            //$table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
            $table->timestamps(); // ✅ adiciona created_at e updated_at

            $table->foreign('modelo_motivo_id')->references('id')->on(prefix('modelo_motivo'))->onDelete('set null');
            $table->foreign('professor_id')->references('id')->on(prefix('professor'))->onDelete('cascade');
            $table->foreign('aluno_id')->references('id')->on(prefix('aluno'))->onDelete('cascade');
            $table->foreign('oferta_id')->references('id')->on(prefix('oferta'))->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on(prefix('escola'))->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on(prefix('regstatus'))->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(prefix('ocorrencia'));
    }
};
