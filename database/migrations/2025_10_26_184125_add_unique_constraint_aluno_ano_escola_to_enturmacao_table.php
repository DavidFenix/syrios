<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tabela = prefix('enturmacao');
        $constraint = 'uq_enturmacao_aluno_ano_escola';

        // 🔹 Garante que não exista índice com o mesmo nome
        try {
            DB::statement("ALTER TABLE {$tabela} DROP INDEX {$constraint}");
        } catch (\Throwable $e) {
            // apenas ignora se ainda não existir
        }

        // 🔹 Cria nova constraint
        DB::statement("
            ALTER TABLE {$tabela}
            ADD CONSTRAINT {$constraint}
            UNIQUE (aluno_id, ano_letivo, school_id)
        ");
    }

    public function down(): void
    {
        $tabela = prefix('enturmacao');
        $constraint = 'uq_enturmacao_aluno_ano_escola';

        // 🔹 Remove a constraint se precisar reverter
        try {
            DB::statement("ALTER TABLE {$tabela} DROP INDEX {$constraint}");
        } catch (\Throwable $e) {
            // ignora se não existir
        }
    }
};
