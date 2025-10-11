<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegStatus;

class RegStatusSeeder extends Seeder
{
    public function run()
    {
        $dados = [
            ['id' => 1, 'descr_s' => 'Ativa'],
            ['id' => 2, 'descr_s' => 'Arquivada'],
        ];

        foreach ($dados as $item) {
            RegStatus::updateOrCreate(['id' => $item['id']], $item);
        }

        $this->command->info('✅ Tabela regstatus populada com sucesso.');
    }
}



/*
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegStatusSeeder extends Seeder
{
    public function run()
    {
        $table = config('prefix.tabelas') . 'regstatus';

        $dados = [
            ['id' => 1, 'descr_s' => 'Ativa'],
            ['id' => 2, 'descr_s' => 'Arquivada'],
        ];

        foreach ($dados as $item) {
            DB::table($table)->updateOrInsert(['id' => $item['id']], $item);
        }

        $this->command->info('✅ Tabela regstatus populada.');
    }
}
*/