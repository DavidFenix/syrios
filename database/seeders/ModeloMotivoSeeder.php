<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModeloMotivoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('syrios_modelo_motivo')->truncate();

        $now = Carbon::now();

        $motivos = [
            ['id'=>1, 'descricao'=>'Conversas paralelas durante a explicação do conteúdo', 'categoria'=>'Comportamento'],
            ['id'=>2, 'descricao'=>'Brincadeiras durante a explicação do conteúdo', 'categoria'=>'Comportamento'],
            ['id'=>3, 'descricao'=>'Falta de respeito com colegas', 'categoria'=>'Disciplina'],
            ['id'=>4, 'descricao'=>'Falta de respeito com professores', 'categoria'=>'Disciplina'],
            ['id'=>5, 'descricao'=>'Não realizou as atividades propostas', 'categoria'=>'Desempenho'],
            ['id'=>6, 'descricao'=>'Saiu da sala sem permissão', 'categoria'=>'Disciplina'],
            ['id'=>7, 'descricao'=>'Mau comportamento em sala', 'categoria'=>'Comportamento'],
            ['id'=>8, 'descricao'=>'Não cumpriu o tempo do intervalo', 'categoria'=>'Pontualidade'],
            ['id'=>9, 'descricao'=>'Atraso frequente', 'categoria'=>'Pontualidade'],
            ['id'=>10, 'descricao'=>'Desrespeito ao professor', 'categoria'=>'Disciplina'],
            ['id'=>11, 'descricao'=>'Fuga do ambiente escolar', 'categoria'=>'Grave'],
            ['id'=>12, 'descricao'=>'Uso indevido de celular em sala', 'categoria'=>'Comportamento'],
            ['id'=>13, 'descricao'=>'Não trouxe o material didático', 'categoria'=>'Desempenho'],
            ['id'=>14, 'descricao'=>'Indisciplina em sala de aula', 'categoria'=>'Comportamento'],
            ['id'=>15, 'descricao'=>'Solicitou sair da sala repetidas vezes (beber água, ir ao banheiro etc.)', 'categoria'=>'Comportamento'],
            ['id'=>16, 'descricao'=>'Comportamento agressivo e criação de conflitos com colegas', 'categoria'=>'Grave'],
            ['id'=>17, 'descricao'=>'Prática de bullying com colegas', 'categoria'=>'Grave'],
            ['id'=>18, 'descricao'=>'Interrompe o professor constantemente', 'categoria'=>'Comportamento'],
            ['id'=>19, 'descricao'=>'Recusa-se a realizar as atividades', 'categoria'=>'Desempenho'],
            ['id'=>20, 'descricao'=>'Desobediência às regras da sala', 'categoria'=>'Disciplina'],
            ['id'=>21, 'descricao'=>'Dificuldade em respeitar a fila ou a ordem de entrada', 'categoria'=>'Comportamento'],
            ['id'=>22, 'descricao'=>'Recusa-se a entregar o celular quando solicitado', 'categoria'=>'Disciplina'],
            ['id'=>23, 'descricao'=>'Usa linguagem inadequada em sala', 'categoria'=>'Disciplina'],
            ['id'=>24, 'descricao'=>'Apresenta desatenção constante durante as aulas', 'categoria'=>'Desempenho'],
            ['id'=>25, 'descricao'=>'Alimenta-se em sala sem autorização', 'categoria'=>'Comportamento'],
            ['id'=>26, 'descricao'=>'Lança objetos ou perturba o ambiente físico da sala', 'categoria'=>'Grave'],
            ['id'=>27, 'descricao'=>'Apresenta descuido com o uniforme escolar', 'categoria'=>'Uniforme'],
            ['id'=>28, 'descricao'=>'Comparece sem uniforme completo', 'categoria'=>'Uniforme'],
            ['id'=>29, 'descricao'=>'Dificuldade em manter o material organizado', 'categoria'=>'Material'],
            ['id'=>30, 'descricao'=>'Não trouxe o caderno ou livro da disciplina', 'categoria'=>'Material'],
            ['id'=>31, 'descricao'=>'Falta de interesse nas atividades', 'categoria'=>'Desempenho'],
            ['id'=>32, 'descricao'=>'Rude ou irônico com funcionários da escola', 'categoria'=>'Disciplina'],
            ['id'=>33, 'descricao'=>'Discussão com colegas durante a aula', 'categoria'=>'Comportamento'],
            ['id'=>34, 'descricao'=>'Desatenção constante e conversa durante avaliações', 'categoria'=>'Comportamento'],
            ['id'=>35, 'descricao'=>'Interfere negativamente na concentração dos colegas', 'categoria'=>'Comportamento'],
            ['id'=>36, 'descricao'=>'Abandono de sala sem justificativa', 'categoria'=>'Grave'],
            ['id'=>37, 'descricao'=>'Desacato a funcionário ou servidor da escola', 'categoria'=>'Grave'],
            ['id'=>38, 'descricao'=>'Danificou material escolar ou patrimônio público', 'categoria'=>'Grave'],
            ['id'=>39, 'descricao'=>'Desobedeceu orientações de segurança escolar', 'categoria'=>'Grave'],
            ['id'=>40, 'descricao'=>'Tentativa de evasão ou ausência prolongada sem justificativa', 'categoria'=>'Grave'],
        ];

        foreach ($motivos as &$m) {
            $m['school_id'] = 1;
            $m['created_at'] = $now;
            $m['updated_at'] = $now;
        }

        DB::table('syrios_modelo_motivo')->insert($motivos);
    }
}




/*
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

        $this->command->info('✅ Modelo Motivo criados para todas as escolas.');
    }
}
*/
