<?php

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
