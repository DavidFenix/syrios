<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Escola;
use App\Models\Usuario;

class MasterFullProtectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a escola master não pode ser excluída (model + controller).
     */
    public function test_escola_master_nao_pode_ser_excluida()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $escolaMaster = Escola::where('is_master', true)->first();
        $this->assertNotNull($escolaMaster, 'Nenhuma escola master encontrada no seeder.');

        // 🔸 Teste direto no model
        $resultado = $escolaMaster->delete();
        $this->assertFalse($resultado, 'A escola master não deveria ser excluída via model.');

        // 🔸 Teste via rota/controller
        $response = $this->delete(route('escolas.destroy', $escolaMaster->id));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'A escola master não pode ser excluída.');

        // 🔸 Garante que o registro continua existindo
        $this->assertDatabaseHas(prefix('escola'), ['id' => $escolaMaster->id]);
    }

    /**
     * Testa se o usuário super master não pode ser excluído (model + controller).
     */
    public function test_usuario_super_master_nao_pode_ser_excluido()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $usuarioMaster = Usuario::where('is_super_master', true)->first();
        $this->assertNotNull($usuarioMaster, 'Nenhum usuário super master encontrado no seeder.');

        // 🔸 Teste direto no model
        $resultado = $usuarioMaster->delete();
        $this->assertFalse($resultado, 'O usuário super master não deveria ser excluído via model.');

        // 🔸 Teste via rota/controller
        $response = $this->delete(route('usuarios.destroy', $usuarioMaster->id));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'O usuário master não pode ser excluído.');

        // 🔸 Garante que o registro continua existindo
        $this->assertDatabaseHas(prefix('usuario'), ['id' => $usuarioMaster->id]);
    }

    /**
     * Testa se o botão de exclusão não aparece para a escola master (na view).
     */
    public function test_view_nao_mostra_botao_excluir_para_escola_master()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $escolaMaster = Escola::where('is_master', true)->first();
        $this->assertNotNull($escolaMaster);

        $response = $this->get(route('escolas.index'));

        // 🔸 O botão não deve aparecer para a escola master
        $response->assertDontSee('form action="' . route('escolas.destroy', $escolaMaster->id) . '"');
    }

    /**
     * Testa se o botão de exclusão não aparece para o usuário master (na view).
     */
    public function test_view_nao_mostra_botao_excluir_para_usuario_super_master()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $usuarioMaster = Usuario::where('is_super_master', true)->first();
        $this->assertNotNull($usuarioMaster);

        $response = $this->get(route('usuarios.index'));

        // 🔸 O botão não deve aparecer para o usuário master
        $response->assertDontSee('form action="' . route('usuarios.destroy', $usuarioMaster->id) . '"');
    }
}
