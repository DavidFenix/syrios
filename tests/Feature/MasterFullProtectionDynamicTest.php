<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Escola;
use App\Models\Usuario;

class MasterFullProtectionDynamicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prefixo dinâmico das rotas (master., escola., secretaria.)
     * É lido automaticamente de .env.testing (variável TEST_PREFIX)
     */
    protected string $prefix;

    /**
     * Inicializa o prefixo antes de cada teste.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 🧠 Garante que os testes tenham uma sessão ativa
        $this->startSession();

        // Lê o prefixo e remove todos os espaços e pontos extras antes/depois
        $rawPrefix = env('TEST_PREFIX', 'master.');
        
        $cleanPrefix = preg_replace('/[^a-zA-Z0-9_]+/', '', $rawPrefix); // mantém apenas letras, números e "_"
        
        $this->prefix = $cleanPrefix . '.';
    }

    /**
     * Testa se a escola master não pode ser excluída (model + controller).
     */
    public function test_escola_master_nao_pode_ser_excluida()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $escolaMaster = Escola::where('is_master', true)->first();
        $this->assertNotNull($escolaMaster, 'Nenhuma escola master encontrada no seeder.');

        // 🔸 Teste direto no Model (proteção no booted)
        $resultado = $escolaMaster->delete();
        $this->assertFalse($resultado, 'A escola master não deveria ser excluída via model.');

        // 🔸 Teste via rota HTTP (Controller)
        $response = $this->delete(route($this->prefix . 'escolas.destroy', $escolaMaster->id));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'A escola master não pode ser excluída.');

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

        // 🔸 Teste direto no Model
        $resultado = $usuarioMaster->delete();
        $this->assertFalse($resultado, 'O usuário super master não deveria ser excluído via model.');

        // 🔸 Teste via rota HTTP (Controller)
        $response = $this->delete(route($this->prefix . 'usuarios.destroy', $usuarioMaster->id));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'O usuário master não pode ser excluído.');

        $this->assertDatabaseHas(prefix('usuario'), ['id' => $usuarioMaster->id]);
    }

    /**
     * Testa se a view não mostra o botão Excluir para a escola master.
     */
    public function test_view_nao_mostra_botao_excluir_para_escola_master()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $escolaMaster = Escola::where('is_master', true)->first();
        $this->assertNotNull($escolaMaster);

        $response = $this->get(route($this->prefix . 'escolas.index'));

        // O botão de exclusão não deve aparecer para escola master
        $response->assertDontSee('form action="' . route($this->prefix . 'escolas.destroy', $escolaMaster->id) . '"');
    }

    /**
     * Testa se a view não mostra o botão Excluir para o usuário super master.
     */
    public function test_view_nao_mostra_botao_excluir_para_usuario_super_master()
    {
        $this->seed(\Database\Seeders\FullDevSeeder::class);

        $usuarioMaster = Usuario::where('is_super_master', true)->first();
        $this->assertNotNull($usuarioMaster);

        $response = $this->get(route($this->prefix . 'usuarios.index'));

        // O botão de exclusão não deve aparecer para o usuário master
        $response->assertDontSee('form action="' . route($this->prefix . 'usuarios.destroy', $usuarioMaster->id) . '"');
    }
}
