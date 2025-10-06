<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Aluno;

class VisaoAlunoSeeder extends Seeder
{
    public function run()
    {
        $table = config('prefix.tabelas') . 'visao_aluno';
        foreach (Aluno::take(10)->get() as $aluno) {
            DB::table($table)->insert([
                'aluno_id'      => $aluno->id,
                'school_id'     => $aluno->school_id,
                'dat_ult_visao' => now(),
            ]);
        }

        $this->command->info('✅ Visões de alunos criadas.');
    }
}
