<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            RegStatusSeeder::class,
            DevSeeder::class,
            RegistrosSeeder::class,
            UsuarioRoleSeeder::class,
            OfertaSeeder::class,
            OcorrenciaSeeder::class,
            SessaoSeeder::class,
            VisaoAlunoSeeder::class,

            // ✅ Adicione esta linha:
            FullDevSeeder::class,
        ]);
    }
}




/*
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     /
    public function run()
    {
        $this->call([
            UsuarioSeeder::class,
        ]);

    }
}*/
