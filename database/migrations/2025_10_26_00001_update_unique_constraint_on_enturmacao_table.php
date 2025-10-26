<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tabela = prefix('enturmacao');

        // 🔹 Remove a constraint antiga (caso exista)
        try {
            DB::statement("ALTER TABLE {$tabela} DROP INDEX uq_enturmacao_unica");
        } catch (\Throwable $e) {
            // apenas ignora se já não existir
        }

        // 🔹 Adiciona a constraint nova (aluno_id, turma_id, school_id)
        DB::statement("
            ALTER TABLE {$tabela}
            ADD CONSTRAINT uq_enturmacao_unica
            UNIQUE (aluno_id, turma_id, school_id)
        ");
    }

    public function down(): void
    {
        $tabela = prefix('enturmacao');

        // 🔹 Reverte para a versão antiga (aluno_id, turma_id, ano_letivo)
        try {
            DB::statement("ALTER TABLE {$tabela} DROP INDEX uq_enturmacao_unica");
        } catch (\Throwable $e) {
            // ignora se já não existir
        }

        DB::statement("
            ALTER TABLE {$tabela}
            ADD CONSTRAINT uq_enturmacao_unica
            UNIQUE (aluno_id, turma_id, ano_letivo)
        ");
    }
};
