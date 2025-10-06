<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{
    Escola, Usuario, Professor, Aluno, Turma, Disciplina
};

class FullDevSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {

            // 🔹 1) Escola Master
            $master = Escola::firstOrCreate(
                ['is_master' => 1],
                ['nome_e' => 'Secretaria do Administrador Master', 'is_master' => 1]
            );

            // 🔹 2) Usuário Super Master
            $superMaster = Usuario::firstOrCreate(
                ['cpf' => 'master'],
                [
                    'school_id'       => $master->id,
                    'senha_hash'      => bcrypt('123456'),
                    'nome_u'          => 'Usuário Master',
                    'status'          => 1,
                    'is_super_master' => 1
                ]
            );

            $this->attachRole($superMaster->id, 5, $master->id); // master

            // 🔹 3) Outros usuários master
            for ($i = 1; $i <= 2; $i++) {
                $user = Usuario::factory()->create([
                    'school_id'       => $master->id,
                    'is_super_master' => 0
                ]);
                $this->attachRole($user->id, 5, $master->id); // master
            }

            // 🔹 4) Secretarias
            $secretarias = collect([
                'Secretaria Crede 08',
                'Secretaria SME Capistrano',
                'Secretaria SME Aratuba',
            ])->map(function ($nome) use ($master) {
                return Escola::factory()->create([
                    'nome_e' => $nome,
                    'secretaria_id' => $master->id,
                    'is_master' => 0
                ]);
            });

            // cria um usuário secretaria para cada secretaria
            $secretarias->each(function ($secretaria) {
                $user = Usuario::factory()->create([
                    'school_id' => $secretaria->id,
                ]);
                $this->attachRole($user->id, 6, $secretaria->id); // secretaria
            });

            // 🔹 5) Escolas regulares
            $escolasDistribuidas = [
                'Secretaria Crede 08' => 4,
                'Secretaria SME Capistrano' => 5,
                'Secretaria SME Aratuba' => 6,
            ];

            $todasEscolas = collect();

            foreach ($escolasDistribuidas as $nomeSecretaria => $quantidade) {
                $sec = $secretarias->firstWhere('nome_e', $nomeSecretaria);
                $escolas = Escola::factory($quantidade)->create([
                    'secretaria_id' => $sec->id,
                    'is_master' => 0,
                ]);
                $todasEscolas = $todasEscolas->merge($escolas);
            }

            // 🔹 Criar usuários com Role escola
            $usuariosEscola = Usuario::factory(10)->create([
                'status' => 1,
            ]);

            $todasEscolas->each(function ($escola) use ($usuariosEscola) {
                // Escolhe usuário para a escola
                $usuario = $usuariosEscola->random();
                $usuario->update(['school_id' => $escola->id]);
                $this->attachRole($usuario->id, 7, $escola->id); // role escola
            });

            // Repetir 5 usuários para escolas sem usuário vinculado
            $semVinculo = $todasEscolas->filter(fn($e) => $e->usuarios()->count() == 0)->take(5);
            $semVinculo->each(function ($escola) use ($usuariosEscola) {
                $usuario = $usuariosEscola->random();
                $this->attachRole($usuario->id, 7, $escola->id);
            });

            // 🔹 6) Turmas, Alunos e Enturmação
            $nomesTurmas = ['1ª Série A', '1ª Série B', '1ª Série C', '1ª Série D'];
            $faker = \Faker\Factory::create('pt_BR'); // inicializa o Faker apenas uma vez

            foreach ($todasEscolas as $escola) {
                foreach ($nomesTurmas as $nomeTurma) {
                    $turno = $faker->randomElement(['Integral', 'Noturno']);
                    $turma = Turma::factory()->create([
                        'school_id' => $escola->id,
                        'serie_turma' => $nomeTurma,
                        'turno' => $turno,
                    ]);

                    // cria 20 alunos e enturma
                    $alunos = Aluno::factory(20)->create(['school_id' => $escola->id]);
                    foreach ($alunos as $aluno) {
                        DB::table(prefix('enturmacao'))->insert([
                            'school_id' => $escola->id,
                            'aluno_id' => $aluno->id,
                            'turma_id' => $turma->id,
                        ]);
                    }
                }
            }

            // 🔹 7) Disciplinas
            $disciplinas = ['Português', 'Matemática', 'História', 'Geografia', 'Ciências'];
            foreach ($todasEscolas as $escola) {
                foreach ($disciplinas as $disc) {
                    DB::table(prefix('disciplina'))->insert([
                        'abr' => Str::slug(substr($disc, 0, 6)),
                        'descr_d' => $disc,
                        'school_id' => $escola->id,
                    ]);
                }
            }

            // 🔹 8) Professores
            $professores = Usuario::factory(20)->create(['status' => 1]);
            foreach ($professores as $prof) {
                $escola = $todasEscolas->random();
                $prof->update(['school_id' => $escola->id]);
                $this->attachRole($prof->id, 2, $escola->id); // professor
                Professor::create(['usuario_id' => $prof->id, 'school_id' => $escola->id]);
            }

            // Garante pelo menos 1 professor por escola
            foreach ($todasEscolas as $escola) {
                if (DB::table(prefix('professor'))->where('school_id', $escola->id)->count() == 0) {
                    $prof = Usuario::factory()->create(['school_id' => $escola->id]);
                    $this->attachRole($prof->id, 2, $escola->id);
                    DB::table(prefix('professor'))->insert([
                        'usuario_id' => $prof->id,
                        'school_id'  => $escola->id,
                    ]);
                    $this->command->warn("👨‍🏫 Professor criado para escola ID {$escola->id}");
                }
            }


            // 🔹 9) Ofertas (professores + disciplinas + turmas)
            foreach ($todasEscolas as $escola) {
                $disciplinasIds = DB::table(prefix('disciplina'))->where('school_id', $escola->id)->pluck('id');
                $turmasIds = DB::table(prefix('turma'))->where('school_id', $escola->id)->pluck('id');
                $profIds = DB::table(prefix('professor'))->where('school_id', $escola->id)->pluck('id');

                // ⚠️ Se a escola não tiver professores, pula
                if ($profIds->isEmpty()) {
                    $this->command->warn("⏩ Nenhum professor na escola ID {$escola->id}, pulando ofertas.");
                    continue;
                }

                foreach ($turmasIds as $turmaId) {
                    foreach ($disciplinasIds as $discId) {
                        DB::table(prefix('oferta'))->insert([
                            'school_id' => $escola->id,
                            'turma_id' => $turmaId,
                            'disciplina_id' => $discId,
                            'professor_id' => $profIds->random(),
                            'status' => 1,
                        ]);
                    }
                }
            }


            // 🔹 10) Diretor de turma
            $todasTurmas = DB::table(prefix('turma'))->get();

            foreach ($todasTurmas as $turma) {
                $ofertas = DB::table(prefix('oferta'))->where('turma_id', $turma->id)->get();

                // ⚠️ Pula turmas sem ofertas
                if ($ofertas->isEmpty()) {
                    $this->command->warn("⏩ Turma ID {$turma->id} sem ofertas — pulando diretor de turma.");
                    continue;
                }

                $profEscolhido = $ofertas->random()->professor_id;

                DB::table(prefix('diretor_turma'))->insertOrIgnore([
                    'professor_id' => $profEscolhido,
                    'turma_id' => $turma->id,
                    'school_id' => $turma->school_id,
                ]);
            }


            $this->command->info('✅ FullDevSeeder executado com sucesso! Sistema completo gerado.');
        });
    }

    private function attachRole($usuarioId, $roleId, $schoolId)
    {
        DB::table(prefix('usuario_role'))->insertOrIgnore([
            'usuario_id' => $usuarioId,
            'role_id'    => $roleId,
            'school_id'  => $schoolId,
        ]);
    }
}
