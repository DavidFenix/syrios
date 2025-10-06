<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Escola, Aluno, Professor, Oferta};

class OcorrenciaSeeder extends Seeder
{
    public function run()
    {
        $table = config('prefix.tabelas') . 'ocorrencia';

        foreach (Escola::all() as $escola) {
            $aluno = Aluno::where('school_id', $escola->id)->first();
            $prof  = Professor::where('school_id', $escola->id)->first();
            $oferta = Oferta::where('school_id', $escola->id)->first();

            if ($aluno && $prof && $oferta) {
                DB::table($table)->insert([
                    'school_id'      => $escola->id,
                    'professor_id'   => $prof->id,
                    'aluno_id'       => $aluno->id,
                    'oferta_id'      => $oferta->id,
                    'registro_id'    => null,
                    'status_id'      => 1,
                    'descricao'      => 'Ocorrência exemplo - ' . $aluno->nome_a,
                    'local'          => 'Sala 1',
                    'criado_em'      => now(),
                    'data_ocorrencia'=> now(),
                ]);
            }
        }

        $this->command->info('✅ Ocorrências criadas.');
    }
}
