<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Escola;

class RegistrosSeeder extends Seeder
{
    public function run()
    {
        $table = config('prefix.tabelas') . 'registros';
        $escolas = Escola::all();

        foreach ($escolas as $escola) {
            DB::table($table)->insert([
                'school_id' => $escola->id,
                'descr_r'   => 'Conversas paralelas durante a exposição do conteúdo'. $escola->nome_e,
                'descr_r'   =>  'Brincadeiras durante a exposição do conteúdo'. $escola->nome_e,
                'descr_r'   =>  'Falta de respeito com os colegas'. $escola->nome_e,
                'descr_r'   =>  'Falta de respeito com os professores'. $escola->nome_e,
                'descr_r'   =>  'Não fez nenhuma atividade proposta'. $escola->nome_e,
                'descr_r'   =>  'Saiu de sala sem permissão'. $escola->nome_e,
                'descr_r'   =>  'Mau comportamento'. $escola->nome_e,
                'descr_r'   =>  'Conversas paralelas durante a exposição do conteúdo'. $escola->nome_e,
                'descr_r'   =>  'Brincadeiras durante a exposição do conteúdo'. $escola->nome_e,
                'descr_r'   =>  'Falta de respeito com os colegas'. $escola->nome_e,
                'descr_r'   =>  'Falta de respeito com os professores'. $escola->nome_e,
                'descr_r'   =>  'Não fez nenhuma atividade proposta'. $escola->nome_e,
                'descr_r'   =>  'Saiu de sala sem permissão'. $escola->nome_e,
                'descr_r'   =>  'Mau comportamento'. $escola->nome_e,
                'descr_r'   =>  'Não cumpriu o tempo do intervalo'. $escola->nome_e,
            ]);
        }

        $this->command->info('✅ Registros criados para todas as escolas.');
    }
}

