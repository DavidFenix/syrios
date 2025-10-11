<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Usuario, Sessao};

class SessaoSeeder extends Seeder
{
    public function run()
    {
        $contador = 0;

        // Cria sessões apenas para os 5 primeiros usuários
        foreach (Usuario::take(5)->get() as $usuario) {
            Sessao::create([
                'usuario_id' => $usuario->id,
                'school_id'  => $usuario->school_id,
            ]);
            $contador++;
        }

        $this->command->info("✅ {$contador} sessões de exemplo criadas com sucesso.");
    }
}



/*
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;

class SessaoSeeder extends Seeder
{
    public function run()
    {
        $table = config('prefix.tabelas') . 'sessao';
        foreach (Usuario::take(5)->get() as $usuario) {
            DB::table($table)->insert([
                'usuario_id' => $usuario->id,
                'school_id'  => $usuario->school_id,
                'criado_em'  => now(),
            ]);
        }

        $this->command->info('✅ Sessões de exemplo criadas.');
    }
}
*/