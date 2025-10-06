<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Escola, Usuario, Professor, Aluno, Disciplina, Turma};

class DevSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {

                // 🔹 1. Cria escola master
            $master = Escola::firstOrCreate(
                ['is_master' => 1],
                [
                    'nome_e'  => 'Secretaria do Administrador Master',
                    'cidade'  => 'Capital',
                    'estado'  => 'CE',
                    'inep'    => '00000001',
                    'is_master' => 1
                ]
            );

            // 🔹 2. Cria usuário master
            Usuario::firstOrCreate(
                ['cpf' => 'master'],
                [
                    'school_id'       => $master->id,
                    'senha_hash'      => bcrypt('123456'),
                    'nome_u'          => 'Usuário Master',
                    'status'          => 1,
                    'is_super_master' => 1
                ]
            );

            // 🔹 3. Cria secretarias
            $smeCapistrano = Escola::firstOrCreate([
                'nome_e' => 'Secretaria SME Capistrano'
            ], [
                'cidade' => 'Capistrano',
                'estado' => 'CE',
                'is_master' => 0
            ]);

            $crede08 = Escola::firstOrCreate([
                'nome_e' => 'Secretaria CREDE 08'
            ], [
                'cidade' => 'Baturité',
                'estado' => 'CE',
                'is_master' => 0
            ]);

            // 🔹 4. Cria escolas filhas e define secretaria_id
            $ubiratan = Escola::firstOrCreate([
                'nome_e' => 'Escola Ubiratan'
            ], [
                'cidade' => 'Capistrano',
                'estado' => 'CE',
                'endereco' => 'Rua José Saraiva Sobrinho',
                'secretaria_id' => $crede08->id,
                'is_master' => 0
            ]);

            $fernandoMota = Escola::firstOrCreate([
                'nome_e' => 'Escola Fernando Mota'
            ], [
                'cidade' => 'Capistrano',
                'estado' => 'CE',
                'secretaria_id' => $smeCapistrano->id,
                'is_master' => 0
            ]);

            // 🔹 Cria 2 secretarias filhas do master
            $secretarias = Escola::factory()
                ->count(2)
                ->create([
                    'secretaria_id' => $master->id,
                    'is_master' => 0,
                ]);

            // 🔹 Para cada secretaria, cria 3 escolas filhas
            $secretarias->each(function ($secretaria) {
                Escola::factory()
                    ->count(3)
                    ->create([
                        'secretaria_id' => $secretaria->id,
                        'is_master' => 0,
                    ]);
            });

            // 🔹 Cria usuário master absoluto
            $masterUser = Usuario::firstOrCreate(
                ['cpf' => 'master'],
                [
                    'school_id'       => $master->id,
                    'senha_hash'      => bcrypt('123456'),
                    'nome_u'          => 'Usuário Master',
                    'status'          => 1,
                    'is_super_master' => 1,
                ]
            );

            // 🔹 Associa o usuário master à role master
            DB::table(prefix('usuario_role'))->insertOrIgnore([
                'usuario_id' => $masterUser->id,
                'role_id'    => 5, // master
                'school_id'  => $master->id,
            ]);

            // 🔹 Cria 2 secretarias vinculadas ao master
            $secretarias = Escola::factory()
                ->count(5)
                ->create([
                    'secretaria_id' => $smeCapistrano->id,
                    'is_master' => 0,
                ]);
                
            // 🔹 Para cada secretaria, cria 3 escolas filhas
            $secretarias->each(function ($secretaria) {

                $escolasFilhas = Escola::factory()
                    ->count(3)
                    ->create([
                        'secretaria_id' => $secretaria->id,
                        'is_master' => 0,
                    ]);

                // 🔹 Para cada escola filha, cria professores, alunos e turmas
                $escolasFilhas->each(function ($escola) {

                    // Usuários
                    Usuario::factory(10)->create(['school_id' => $escola->id]);

                    // Professores
                    Professor::factory(5)->create(['school_id' => $escola->id]);

                    // Disciplinas
                    Disciplina::factory(7)->create(['school_id' => $escola->id]);

                    // Turmas
                    Turma::factory(8)->create(['school_id' => $escola->id]);

                    // Alunos
                    Aluno::factory(30)->create(['school_id' => $escola->id]);
                });
            });

            // 🔹 Agora criamos usuários aleatórios distribuídos por escolas
            $todasEscolas = Escola::all();

            $todasEscolas->each(function ($escola) {
                // cria 5 usuários por escola
                $usuarios = Usuario::factory(5)->create(['school_id' => $escola->id]);

                // para cada usuário, define 1–2 roles aleatórias
                foreach ($usuarios as $usuario) {
                    $roles = DB::table(prefix('role'))->pluck('id')->shuffle()->take(rand(1, 2));

                    foreach ($roles as $roleId) {
                        DB::table(prefix('usuario_role'))->insertOrIgnore([
                            'usuario_id' => $usuario->id,
                            'role_id'    => $roleId,
                            'school_id'  => $escola->id,
                        ]);
                    }

                    // se o usuário tiver a role professor, cria o vínculo na tabela professor
                    if (in_array(2, $roles->toArray())) { // role_id 2 = professor
                        \App\Models\Professor::create([
                            'usuario_id' => $usuario->id,
                            'school_id'  => $escola->id,
                        ]);
                    }
                }

                // cria disciplinas, turmas e alunos
                \App\Models\Disciplina::factory(3)->create(['school_id' => $escola->id]);
                \App\Models\Turma::factory(2)->create(['school_id' => $escola->id]);
                \App\Models\Aluno::factory(10)->create(['school_id' => $escola->id]);
            });

            $this->command->info('✅ Dados com múltiplas roles e múltiplas escolas criados com sucesso!');
        });
    }
}



/*
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Escola, Usuario, Professor, Aluno, Disciplina, Turma};
use Illuminate\Support\Facades\DB;

class DevSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {

            // 🔹 1. Cria escola master
        $master = Escola::firstOrCreate(
            ['is_master' => 1],
            [
                'nome_e'  => 'Secretaria do Administrador Master',
                'cidade'  => 'Capital',
                'estado'  => 'CE',
                'inep'    => '00000001',
                'is_master' => 1
            ]
        );

        // 🔹 2. Cria usuário master
        Usuario::firstOrCreate(
            ['cpf' => 'master'],
            [
                'school_id'       => $master->id,
                'senha_hash'      => bcrypt('123456'),
                'nome_u'          => 'Usuário Master',
                'status'          => 1,
                'is_super_master' => 1
            ]
        );

        // 🔹 3. Cria secretarias
        $smeCapistrano = Escola::firstOrCreate([
            'nome_e' => 'Secretaria SME Capistrano'
        ], [
            'cidade' => 'Capistrano',
            'estado' => 'CE',
            'is_master' => 0
        ]);

        $crede08 = Escola::firstOrCreate([
            'nome_e' => 'Secretaria CREDE 08'
        ], [
            'cidade' => 'Baturité',
            'estado' => 'CE',
            'is_master' => 0
        ]);

        // 🔹 4. Cria escolas filhas e define secretaria_id
        $ubiratan = Escola::firstOrCreate([
            'nome_e' => 'Escola Ubiratan'
        ], [
            'cidade' => 'Capistrano',
            'estado' => 'CE',
            'endereco' => 'Rua José Saraiva Sobrinho',
            'secretaria_id' => $crede08->id,
            'is_master' => 0
        ]);

        $fernandoMota = Escola::firstOrCreate([
            'nome_e' => 'Escola Fernando Mota'
        ], [
            'cidade' => 'Capistrano',
            'estado' => 'CE',
            'secretaria_id' => $smeCapistrano->id,
            'is_master' => 0
        ]);

        // 🔹 Cria 2 secretarias vinculadas ao master
        $secretarias = Escola::factory()
            ->count(5)
            ->create([
                'secretaria_id' => $smeCapistrano->id,
                'is_master' => 0,
            ]);

        // 🔹 Para cada secretaria, cria 3 escolas filhas
        $secretarias->each(function ($secretaria) {

            $escolasFilhas = Escola::factory()
                ->count(3)
                ->create([
                    'secretaria_id' => $secretaria->id,
                    'is_master' => 0,
                ]);

            // 🔹 Para cada escola filha, cria professores, alunos e turmas
            $escolasFilhas->each(function ($escola) {

                // Usuários
                Usuario::factory(10)->create(['school_id' => $escola->id]);

                // Professores
                Professor::factory(5)->create(['school_id' => $escola->id]);

                // Disciplinas
                Disciplina::factory(7)->create(['school_id' => $escola->id]);

                // Turmas
                Turma::factory(8)->create(['school_id' => $escola->id]);

                // Alunos
                Aluno::factory(30)->create(['school_id' => $escola->id]);
            });
        });

            $this->command->info('✅ Hierarquia completa de escolas criada com sucesso!');
        });
    }
}
*/

/*
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Escola, Usuario, Aluno, Disciplina, Professor, Turma};

class DevSeeder extends Seeder
{
    public function run()
    {
        // 🔹 1. Cria escola master
        $master = Escola::firstOrCreate(
            ['is_master' => 1],
            [
                'nome_e'  => 'Secretaria do Administrador Master',
                'cidade'  => 'Capital',
                'estado'  => 'CE',
                'inep'    => '00000001',
                'is_master' => 1
            ]
        );

        // 🔹 2. Cria usuário master
        Usuario::firstOrCreate(
            ['cpf' => 'master'],
            [
                'school_id'       => $master->id,
                'senha_hash'      => bcrypt('123456'),
                'nome_u'          => 'Usuário Master',
                'status'          => 1,
                'is_super_master' => 1
            ]
        );

        // 🔹 3. Cria secretarias
        $smeCapistrano = Escola::firstOrCreate([
            'nome_e' => 'Secretaria SME Capistrano'
        ], [
            'cidade' => 'Capistrano',
            'estado' => 'CE',
            'is_master' => 0
        ]);

        $crede08 = Escola::firstOrCreate([
            'nome_e' => 'Secretaria CREDE 08'
        ], [
            'cidade' => 'Baturité',
            'estado' => 'CE',
            'is_master' => 0
        ]);

        // 🔹 4. Cria escolas filhas e define secretaria_id
        $ubiratan = Escola::firstOrCreate([
            'nome_e' => 'Escola Ubiratan'
        ], [
            'cidade' => 'Capistrano',
            'estado' => 'CE',
            'endereco' => 'Rua José Saraiva Sobrinho',
            'secretaria_id' => $crede08->id,
            'is_master' => 0
        ]);

        $fernandoMota = Escola::firstOrCreate([
            'nome_e' => 'Escola Fernando Mota'
        ], [
            'cidade' => 'Capistrano',
            'estado' => 'CE',
            'secretaria_id' => $smeCapistrano->id,
            'is_master' => 0
        ]);

        // Outras escolas
        Escola::factory(3)->create();

        // Professores
        Professor::factory(5)->create();

        // Usuários
        Usuario::factory(10)->create();

        // Alunos
        Aluno::factory(30)->create();

        // Disciplinas
        Disciplina::factory(10)->create();

        // Turmas
        Turma::factory(8)->create();

        $this->command->info('✅ Dados de desenvolvimento criados com sucesso!');
    }
}
*/