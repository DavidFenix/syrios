<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🧩 1. Disciplina — Evita abreviações duplicadas na mesma escola
        Schema::table(prefix('disciplina'), function (Blueprint $table) {
            if (!Schema::hasColumn(prefix('disciplina'), 'abr')) {
                return; // segurança
            }
            $table->unique(['school_id', 'abr'], 'uq_disciplina_escola_abr');
        });

        // 🏫 2. Turma — Evita duplicidade de série/turno dentro da escola
        Schema::table(prefix('turma'), function (Blueprint $table) {
            if (!Schema::hasColumn(prefix('turma'), 'serie_turma')) {
                return;
            }
            $table->unique(['school_id', 'serie_turma', 'turno'], 'uq_turma_identificacao');
        });

        // 👩‍🎓 3. Enturmação — Impede aluno duplicado na mesma turma/ano
        Schema::table(prefix('enturmacao'), function (Blueprint $table) {
            if (!Schema::hasColumn(prefix('enturmacao'), 'aluno_id')) {
                return;
            }
            $table->unique(['aluno_id', 'turma_id', 'ano_letivo'], 'uq_enturmacao_unica');
        });

        //vamos permitir
        Schema::table(prefix('enturmacao'), function (Blueprint $table) {
            $table->unsignedBigInteger('turma_id')->nullable()->change();
        });

    }

    public function down(): void
    {
        // Reversão segura — remove as UQs apenas se existirem
        Schema::table(prefix('disciplina'), function (Blueprint $table) {
            try { $table->dropUnique('uq_disciplina_escola_abr'); } catch (\Throwable $e) {}
        });

        Schema::table(prefix('turma'), function (Blueprint $table) {
            try { $table->dropUnique('uq_turma_identificacao'); } catch (\Throwable $e) {}
        });

        Schema::table(prefix('enturmacao'), function (Blueprint $table) {
            try { $table->dropUnique('uq_enturmacao_unica'); } catch (\Throwable $e) {}
        });
    }
};
