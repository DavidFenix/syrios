<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Escola;

class UsuarioRoleSeeder extends Seeder
{
    public function run()
    {
        $table = config('prefix.tabelas') . 'usuario_role';
        $usuarios = Usuario::all();

        foreach ($usuarios as $usuario) {
            $roleId = match (true) {
                $usuario->is_super_master => 5, // master
                $usuario->school_id == 1  => 5, // master
                default                   => 2, // professor
            };

            DB::table($table)->updateOrInsert([
                'usuario_id' => $usuario->id,
                'role_id'    => $roleId,
                'school_id'  => $usuario->school_id
            ]);
        }

        $this->command->info('✅ Tabela usuario_role populada.');
    }
}
