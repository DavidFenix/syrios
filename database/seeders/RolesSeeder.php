<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $table = config('prefix.tabelas') . 'role';

        $roles = [
            ['id' => 1, 'role_name' => 'admin'],
            ['id' => 2, 'role_name' => 'professor'],
            ['id' => 3, 'role_name' => 'gestor'],
            ['id' => 4, 'role_name' => 'pais'],
            ['id' => 5, 'role_name' => 'master'],
            ['id' => 6, 'role_name' => 'secretaria'],
            ['id' => 7, 'role_name' => 'escola'],
        ];

        foreach ($roles as $role) {
            DB::table($table)->updateOrInsert(['id' => $role['id']], $role);
        }

        $this->command->info('✅ Tabela de roles populada com sucesso!');
    }
}
