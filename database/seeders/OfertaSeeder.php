<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Turma, Disciplina, Professor, Escola};

class OfertaSeeder extends Seeder
{
    public function run()
    {
        $table = config('prefix.tabelas') . 'oferta';
        $escolas = Escola::all();

        foreach ($escolas as $escola) {
            $turma = Turma::where('school_id', $escola->id)->first();
            $disc = Disciplina::where('school_id', $escola->id)->first();
            $prof = Professor::where('school_id', $escola->id)->first();

            if ($turma && $disc && $prof) {
                DB::table($table)->insert([
                    'school_id'     => $escola->id,
                    'turma_id'      => $turma->id,
                    'disciplina_id' => $disc->id,
                    'professor_id'  => $prof->id,
                    'status'        => 1,
                ]);
            }
        }

        $this->command->info('✅ Ofertas geradas para cada escola.');
    }
}
