<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Escola, Usuario};

class SecretariaExemploSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {

            $seduc = Escola::firstOrCreate(
                ['nome_e' => 'SEDUC - Secretaria da Educação'],
                [
                    'cidade' => 'Capistrano',
                    'estado' => 'CE',
                    'endereco' => 'Rua 2, Conjunto Novo Planalto',
                    'is_master' => 0,
                ]
            );

            $ubiratan = Escola::firstOrCreate(
                ['nome_e' => 'EEMTI Dep. Ubiratan Diniz de Aguiar'],
                [
                    'cidade' => 'Capistrano',
                    'estado' => 'CE',
                    'endereco' => 'Rua José Saraiva Sobrinho',
                    'secretaria_id' => $seduc->id,
                    'is_master' => 0,
                ]
            );

            $fmota = Escola::firstOrCreate(
                ['nome_e' => 'EEFTI Fernando Cavalcante Mota'],
                [
                    'cidade' => 'Capistrano',
                    'estado' => 'CE',
                    'endereco' => 'Rua José Saraiva Sobrinho',
                    'secretaria_id' => $seduc->id,
                    'is_master' => 0,
                ]
            );

            $david = Usuario::firstOrCreate(
                ['cpf' => '97351938334'],
                [
                    'school_id' => $seduc->id,
                    'senha_hash' => bcrypt('123456'),
                    'nome_u' => 'David Costa',
                    'status' => 1,
                    'is_super_master' => 0,
                ]
            );

            // Roles do David
            $vinculosDavid = [
                ['role_id' => 6, 'school_id' => $seduc->id],   // secretaria
                ['role_id' => 7, 'school_id' => $ubiratan->id], // escola
                ['role_id' => 2, 'school_id' => $ubiratan->id], // professor
                ['role_id' => 7, 'school_id' => $fmota->id],    // escola
                ['role_id' => 2, 'school_id' => $fmota->id],    // professor
            ];

            foreach ($vinculosDavid as $v) {
                DB::table(prefix('usuario_role'))->insertOrIgnore(array_merge($v, [
                    'usuario_id' => $david->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            $ravi = Usuario::firstOrCreate(
                ['cpf' => '97351938335'],
                [
                    'school_id' => $seduc->id,
                    'senha_hash' => bcrypt('123456'),
                    'nome_u' => 'Ravi Costa',
                    'status' => 1,
                    'is_super_master' => 0,
                ]
            );

            DB::table(prefix('usuario_role'))->insertOrIgnore([
                'usuario_id' => $ravi->id,
                'role_id' => 6,
                'school_id' => $seduc->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $udavi = Usuario::firstOrCreate(
                ['cpf' => '97351938336'],
                [
                    'school_id' => $ubiratan->id,
                    'senha_hash' => bcrypt('123456'),
                    'nome_u' => 'Usuário 1 por David Costa',
                    'status' => 1,
                    'is_super_master' => 0,
                ]
            );

            $uravi = Usuario::firstOrCreate(
                ['cpf' => '97351938337'],
                [
                    'school_id' => $fmota->id,
                    'senha_hash' => bcrypt('123456'),
                    'nome_u' => 'Usuário 2 por Ravi Costa',
                    'status' => 1,
                    'is_super_master' => 0,
                ]
            );

            echo "✅ Secretaria e usuários criados com sucesso!\n";
        });
    }
}
