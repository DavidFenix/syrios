<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Escola;
use App\Models\Usuario;

class MasterProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_nao_pode_excluir_escola_master()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $escolaMaster = Escola::where('is_master', true)->first();
        $resultado = $escolaMaster->delete();

        $this->assertFalse($resultado, 'A escola master não deveria ser excluída.');
        $this->assertDatabaseHas(prefix('escola'), ['id' => $escolaMaster->id]);
    }

    public function test_nao_pode_excluir_usuario_super_master()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $usuarioMaster = Usuario::where('is_super_master', true)->first();
        $resultado = $usuarioMaster->delete();

        $this->assertFalse($resultado, 'O usuário super master não deveria ser excluído.');
        $this->assertDatabaseHas(prefix('usuario'), ['id' => $usuarioMaster->id]);
    }
}
