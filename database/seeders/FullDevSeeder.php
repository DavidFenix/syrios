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

            // 1️⃣ Cria a escola master (secretaria central)
            $master = Escola::firstOrCreate(
                ['is_master' => 1],
                [
                    'nome_e'        => 'Secretaria do Administrador Master',
                    'is_master'     => 1,
                    'secretaria_id' => null,
                ]
            );

            // 2️⃣ Cria o usuário Super Master
            $superMaster = Usuario::firstOrCreate(
                ['cpf' => 'master'],
                [
                    'school_id'       => $master->id,
                    'senha_hash'      => bcrypt('123456'),
                    'nome_u'          => 'Usuário Master',
                    'status'          => 1,
                    'is_super_master' => 1,
                ]
            );

            $this->attachRole($superMaster->id, 5, $master->id); // role_id=5 → master

            // 3️⃣ Cria outros masters comuns
            for ($i = 1; $i <= 2; $i++) {
                $user = Usuario::factory()->create([
                    'school_id'       => $master->id,
                    'is_super_master' => 0,
                ]);
                $this->attachRole($user->id, 5, $master->id);
            }

            // 4️⃣ Cria secretarias (mães)
            $secretarias = collect([
                'Secretaria Crede 08',
                'Secretaria SME Capistrano',
                'Secretaria SME Aratuba',
            ])->map(function ($nome) {
                return Escola::factory()->create([
                    'nome_e'        => $nome,
                    'secretaria_id' => null,
                    'is_master'     => 0,
                ]);
            });

            // Cria usuários vinculados às secretarias
            $secretarias->each(function ($secretaria) {
                $user = Usuario::factory()->create([
                    'school_id'       => $secretaria->id,
                    'is_super_master' => 0,
                ]);
                $this->attachRole($user->id, 6, $secretaria->id); // role_id=6 → secretaria
            });

            // 5️⃣ Cria escolas regulares e as vincula às secretarias
            $faker = \Faker\Factory::create('pt_BR');
            $escolasDistribuidas = [
                'Secretaria Crede 08'       => 4,
                'Secretaria SME Capistrano' => 5,
                'Secretaria SME Aratuba'    => 6,
            ];

            $todasEscolas = collect();

            foreach ($escolasDistribuidas as $nomeSecretaria => $quantidade) {
                $sec = $secretarias->firstWhere('nome_e', $nomeSecretaria);

                for ($i = 0; $i < $quantidade; $i++) {
                    $escola = Escola::create([
                        'inep'          => str_pad((string) $faker->numberBetween(1, 99999999), 8, '0', STR_PAD_LEFT),
                        'cnpj'          => $faker->numerify('##.###.###/####-##'),
                        'nome_e'        => $faker->company(),
                        'cidade'        => $faker->city(),
                        'estado'        => $faker->state(),
                        'endereco'      => $faker->streetAddress(),
                        'telefone'      => $faker->numerify('##-#####-####'),
                        'secretaria_id' => $sec->id,
                        'is_master'     => 0,
                    ]);
                    $todasEscolas->push($escola);
                }
            }

            // (restante do seeder: turmas, professores, alunos, etc.)
        });

        // 6️⃣ Normalizações pós-seed
        $this->normalizeHierarchy();

        $this->command->info('✅ FullDevSeeder finalizado e hierarquia normalizada com sucesso!');
    }

    /**
     * Anexa uma role ao usuário de forma segura.
     */
    private function attachRole($usuarioId, $roleId, $schoolId)
    {
        DB::table(prefix('usuario_role'))->insertOrIgnore([
            'usuario_id' => $usuarioId,
            'role_id'    => $roleId,
            'school_id'  => $schoolId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Corrige inconsistências hierárquicas entre escolas mães e filhas.
     */
    private function normalizeHierarchy()
    {
        $table = prefix('escola');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Fase 1 — promoção de escolas que se tornaram mães
        $idsComFilhas = DB::table($table)
            ->whereNotNull('secretaria_id')
            ->pluck('secretaria_id')
            ->unique()
            ->filter()
            ->values();

        if ($idsComFilhas->isNotEmpty()) {
            $idsList = $idsComFilhas->implode(',');
            $count = DB::update("UPDATE {$table} SET secretaria_id = NULL WHERE id IN ({$idsList})");
            $this->command->warn("🔄 Promoção automática: {$count} escolas tornaram-se MÃES (secretaria_id=null).");
        } else {
            $this->command->warn('✅ Nenhuma escola mãe precisou ser promovida.');
        }

        // Fase 2 — mães de mães (cadeias duplas)
        $violacoes = DB::select("
            SELECT DISTINCT mae.id
            FROM {$table} filha
            JOIN {$table} mae ON mae.id = filha.secretaria_id
            WHERE mae.secretaria_id IS NOT NULL
        ");

        if (!empty($violacoes)) {
            $ids = collect($violacoes)->pluck('id')->implode(',');
            $count2 = DB::update("UPDATE {$table} SET secretaria_id = NULL WHERE id IN ({$ids})");
            $this->command->warn("🛠️ Normalização adicional: {$count2} mães ainda tinham secretaria_id — corrigido.");
        } else {
            $this->command->warn('✅ Nenhuma violação de cadeia mãe→mãe detectada.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}


/*
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
        // 🔹 Execução principal dentro da transação
        DB::transaction(function () {

            // 1) Escola Master
            $master = Escola::firstOrCreate(
                ['is_master' => 1],
                ['nome_e' => 'Secretaria do Administrador Master', 'is_master' => 1]
            );

            // 2) Usuário Super Master
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
            $this->attachRole($superMaster->id, 5, $master->id);

            // 3) Outros usuários master
            for ($i = 1; $i <= 2; $i++) {
                $user = Usuario::factory()->create([
                    'school_id'       => $master->id,
                    'is_super_master' => 0
                ]);
                $this->attachRole($user->id, 5, $master->id);
            }

            // 4) Secretarias (sempre MÃE → secretaria_id = null)
            $secretarias = collect([
                'Secretaria Crede 08',
                'Secretaria SME Capistrano',
                'Secretaria SME Aratuba',
            ])->map(function ($nome) {
                return Escola::factory()->create([
                    'nome_e'        => $nome,
                    'secretaria_id' => null,
                    'is_master'     => 0,
                ]);
            });

            // cria usuário para cada secretaria
            $secretarias->each(function ($secretaria) {
                $user = Usuario::factory()->create(['school_id' => $secretaria->id]);
                $this->attachRole($user->id, 6, $secretaria->id);
            });

            // 5) Escolas regulares
            $faker = \Faker\Factory::create('pt_BR');
            $escolasDistribuidas = [
                'Secretaria Crede 08'       => 4,
                'Secretaria SME Capistrano' => 5,
                'Secretaria SME Aratuba'    => 6,
            ];
            $todasEscolas = collect();

            foreach ($escolasDistribuidas as $nomeSecretaria => $quantidade) {
                $sec = $secretarias->firstWhere('nome_e', $nomeSecretaria);

                for ($i = 0; $i < $quantidade; $i++) {
                    $id = DB::table(prefix('escola'))->insertGetId([
                        'inep'          => str_pad((string) $faker->numberBetween(1, 99999999), 8, '0', STR_PAD_LEFT),
                        'cnpj'          => $faker->numerify('##.###.###/####-##'),
                        'nome_e'        => $faker->company(),
                        'cidade'        => $faker->city(),
                        'estado'        => $faker->state(),
                        'endereco'      => $faker->streetAddress(),
                        'telefone'      => $faker->numerify('##-#####-####'),
                        'secretaria_id' => $sec->id,
                        'is_master'     => 0,
                        'criado_em'     => now(),
                    ]);
                    $todasEscolas->push(Escola::find($id));
                }
            }

            // (resto omitido por brevidade — turmas, alunos, professores, etc.)
        });

        // ⚠️ FORA da transação — promoção auditável
        $table = prefix('escola');

        // desativa FK temporariamente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 🔧 Fase 1: mães diretas
        $idsComFilhas = DB::table($table)
            ->whereNotNull('secretaria_id')
            ->pluck('secretaria_id')
            ->unique()
            ->filter()
            ->values();

        if ($idsComFilhas->isNotEmpty()) {
            $idsList = $idsComFilhas->implode(',');
            $count = DB::update("UPDATE {$table} SET secretaria_id = NULL WHERE id IN ({$idsList})");
            $this->command->warn("🔄 Promoção automática: {$count} escolas tornaram-se MÃES (secretaria_id=null).");
        } else {
            $this->command->warn('✅ Nenhuma escola mãe precisou ser promovida.');
        }

        // 🔧 Fase 2: mães de mães (cadeias duplas)
        $violacoes = DB::select("
            SELECT DISTINCT mae.id
            FROM {$table} filha
            JOIN {$table} mae ON mae.id = filha.secretaria_id
            WHERE mae.secretaria_id IS NOT NULL
        ");
        if (!empty($violacoes)) {
            $ids = collect($violacoes)->pluck('id')->implode(',');
            $count2 = DB::update("UPDATE {$table} SET secretaria_id = NULL WHERE id IN ({$ids})");
            $this->command->warn("🛠️ Normalização adicional: {$count2} mães ainda tinham secretaria_id — corrigido.");
        } else {
            $this->command->warn('✅ Nenhuma violação de cadeia mãe→mãe detectada.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ FullDevSeeder finalizado e hierarquia de secretarias normalizada com sucesso!');
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
*/