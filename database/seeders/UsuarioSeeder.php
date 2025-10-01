<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // cria usuário master se não existir
        Usuario::firstOrCreate(
            ['cpf' => 'master'],
            [
                'school_id'  => 1, // id da escola "Administração do Syrios"
                'nome_u'     => 'Usuário Master',
                'senha_hash' => Hash::make('123456'), // senha padrão
                'status'     => 1
            ]
        );
    }
}
