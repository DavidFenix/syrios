<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🔁 Tabelas temporais
        $temporais = [
            prefix('diretor_turma'),
            prefix('enturmacao'),
            prefix('ocorrencia'),
            prefix('oferta'),
        ];

        foreach ($temporais as $tabela) {
            Schema::table($tabela, function (Blueprint $table) use ($tabela) {
                if (!Schema::hasColumn($tabela, 'ano_letivo')) {
                    $table->integer('ano_letivo')->default(date('Y'))->after('school_id');
                }

                if (!Schema::hasColumn($tabela, 'vigente')) {
                    $table->boolean('vigente')->default(true)->after('ano_letivo');
                }
            });
        }
    }

    public function down(): void
    {
        $temporais = [
            prefix('diretor_turma'),
            prefix('enturmacao'),
            prefix('ocorrencia'),
            prefix('oferta'),
        ];

        foreach ($temporais as $tabela) {
            Schema::table($tabela, function (Blueprint $table) {
                $table->dropColumn(['ano_letivo', 'vigente']);
            });
        }
    }
};
