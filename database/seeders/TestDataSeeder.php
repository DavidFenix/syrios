<?php
/**
 * Seeder: TestDataSeeder
 * Cria 20 usuários Faker, professores e vínculos para teste.
 */
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Professor;
use App\Models\Escola;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Gera 20 usuários se ainda não houver muitos
        $count = Usuario::count();
        if ($count < 20) {
            Usuario::factory()->count(20 - $count)->create();
        }

        // Vincula aleatoriamente professores a escolas
        $usuarios = Usuario::inRandomOrder()->take(10)->get();
        foreach ($usuarios as $u) {
            Professor::firstOrCreate([
                'usuario_id' => $u->id,
                'school_id'  => Escola::inRandomOrder()->value('id') ?? 1
            ]);
        }
    }
}
