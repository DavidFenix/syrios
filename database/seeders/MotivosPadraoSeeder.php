<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotivosPadraoSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1; // ou use uma escola master como padrão inicial

        $motivos = [
            ['descricao' => 'Uso de celular em sala', 'categoria' => 'Comportamento'],
            ['descricao' => 'Desrespeito ao professor', 'categoria' => 'Disciplina'],
            ['descricao' => 'Atraso frequente', 'categoria' => 'Pontualidade'],
            ['descricao' => 'Conversas durante explicação', 'categoria' => 'Comportamento'],
            ['descricao' => 'Agressão verbal ou física', 'categoria' => 'Grave'],
            ['descricao' => 'Fuga do ambiente escolar', 'categoria' => 'Grave'],
        ];

        foreach ($motivos as $motivo) {
            DB::table(prefix('modelo_motivo'))->insert([
                'school_id' => $schoolId,
                'descricao' => $motivo['descricao'],
                'categoria' => $motivo['categoria'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
