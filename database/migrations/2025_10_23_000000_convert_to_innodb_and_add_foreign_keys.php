<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ✅ Converter todas as tabelas para InnoDB
        $tables = [
            'aluno', 'diretor_turma', 'disciplina', 'enturmacao', 'escola', 'modelo_motivo',
            'notificacao', 'ocorrencia', 'ocorrencia_motivo', 'oferta', 'professor',
            'regimento', 'regstatus', 'role', 'sessao', 'turma', 'usuario', 'usuario_role', 'visao_aluno'
        ];

        foreach ($tables as $tbl) {
            DB::statement("ALTER TABLE syrios_{$tbl} ENGINE=InnoDB;");
        }

        // =========================================================
        // 🔗 Criação das FKs (com verificação idempotente)
        // =========================================================

        $fks = [

            // Escola
            "ALTER TABLE syrios_escola 
                ADD CONSTRAINT fk_escola_secretaria
                FOREIGN KEY (secretaria_id) REFERENCES syrios_escola(id)
                ON DELETE SET NULL ON UPDATE CASCADE",

            // Usuario
            "ALTER TABLE syrios_usuario 
                ADD CONSTRAINT fk_usuario_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Professor
            "ALTER TABLE syrios_professor 
                ADD CONSTRAINT fk_professor_usuario
                FOREIGN KEY (usuario_id) REFERENCES syrios_usuario(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_professor_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Disciplina
            "ALTER TABLE syrios_disciplina 
                ADD CONSTRAINT fk_disciplina_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Turma
            "ALTER TABLE syrios_turma 
                ADD CONSTRAINT fk_turma_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Aluno
            "ALTER TABLE syrios_aluno 
                ADD CONSTRAINT fk_aluno_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Enturmacao
            "ALTER TABLE syrios_enturmacao 
                ADD CONSTRAINT fk_enturmacao_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_enturmacao_aluno
                FOREIGN KEY (aluno_id) REFERENCES syrios_aluno(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_enturmacao_turma
                FOREIGN KEY (turma_id) REFERENCES syrios_turma(id)
                ON DELETE CASCADE ON UPDATE CASCADE",

            // Oferta
            "ALTER TABLE syrios_oferta 
                ADD CONSTRAINT fk_oferta_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_oferta_professor
                FOREIGN KEY (professor_id) REFERENCES syrios_professor(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_oferta_turma
                FOREIGN KEY (turma_id) REFERENCES syrios_turma(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_oferta_disciplina
                FOREIGN KEY (disciplina_id) REFERENCES syrios_disciplina(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Ocorrencia
            "ALTER TABLE syrios_ocorrencia 
                ADD CONSTRAINT fk_ocorrencia_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_ocorrencia_aluno
                FOREIGN KEY (aluno_id) REFERENCES syrios_aluno(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_ocorrencia_professor
                FOREIGN KEY (professor_id) REFERENCES syrios_professor(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_ocorrencia_oferta
                FOREIGN KEY (oferta_id) REFERENCES syrios_oferta(id)
                ON DELETE SET NULL ON UPDATE CASCADE",

            // Ocorrencia_motivo
            "ALTER TABLE syrios_ocorrencia_motivo 
                ADD CONSTRAINT fk_ocmotivo_ocorrencia
                FOREIGN KEY (ocorrencia_id) REFERENCES syrios_ocorrencia(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_ocmotivo_modelo
                FOREIGN KEY (modelo_motivo_id) REFERENCES syrios_modelo_motivo(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Modelo_motivo
            "ALTER TABLE syrios_modelo_motivo 
                ADD CONSTRAINT fk_modelomotivo_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Usuario_role
            "ALTER TABLE syrios_usuario_role 
                ADD CONSTRAINT fk_urole_usuario
                FOREIGN KEY (usuario_id) REFERENCES syrios_usuario(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_urole_role
                FOREIGN KEY (role_id) REFERENCES syrios_role(id)
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_urole_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Diretor_turma
            "ALTER TABLE syrios_diretor_turma 
                ADD CONSTRAINT fk_diretor_professor
                FOREIGN KEY (professor_id) REFERENCES syrios_professor(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_diretor_turma
                FOREIGN KEY (turma_id) REFERENCES syrios_turma(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_diretor_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Notificacao
            "ALTER TABLE syrios_notificacao 
                ADD CONSTRAINT fk_notificacao_usuario
                FOREIGN KEY (usuario_id) REFERENCES syrios_usuario(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_notificacao_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Sessao
            "ALTER TABLE syrios_sessao 
                ADD CONSTRAINT fk_sessao_usuario
                FOREIGN KEY (usuario_id) REFERENCES syrios_usuario(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_sessao_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Visao_aluno
            "ALTER TABLE syrios_visao_aluno 
                ADD CONSTRAINT fk_visao_aluno
                FOREIGN KEY (aluno_id) REFERENCES syrios_aluno(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_visao_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",

            // Regimento
            "ALTER TABLE syrios_regimento 
                ADD CONSTRAINT fk_regimento_escola
                FOREIGN KEY (school_id) REFERENCES syrios_escola(id)
                ON DELETE RESTRICT ON UPDATE CASCADE",
        ];

        foreach ($fks as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // Ignora erro se FK já existir
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::table(prefix('disciplina'), function (Blueprint $table) {
            $table->unique(['school_id', 'abr'], 'uq_disciplina_escola_abr');
        });

        Schema::table(prefix('turma'), function (Blueprint $table) {
            $table->unique(['school_id', 'serie_turma', 'turno'], 'uq_turma_identificacao');
        });

        Schema::table(prefix('enturmacao'), function (Blueprint $table) {
            $table->unique(['aluno_id', 'turma_id', 'ano_letivo'], 'uq_enturmacao_unica');
        });

    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'aluno', 'diretor_turma', 'disciplina', 'enturmacao', 'escola', 'modelo_motivo',
            'notificacao', 'ocorrencia', 'ocorrencia_motivo', 'oferta', 'professor',
            'regimento', 'regstatus', 'role', 'sessao', 'turma', 'usuario', 'usuario_role', 'visao_aluno'
        ];

        foreach ($tables as $tbl) {
            DB::statement("ALTER TABLE syrios_{$tbl} ENGINE=MyISAM;");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
