<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Escola;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 🛡️ MasterSecurityBehaviorTest
 * ----------------------------------------------
 * Testes comportamentais para garantir as regras
 * de proteção das operações do Master:
 *  - Proteção da Escola Master
 *  - Proteção do Usuário Super Master
 *  - Ocultação de botões nas views
 * 
 * Foco: validar comportamento (não mensagens)
 * ----------------------------------------------
 */
class MasterSecurityBehaviorTest extends TestCase
{
    use RefreshDatabase;

    protected string $prefix;

    protected function setUp(): void
    {
        parent::setUp();

        // 🔄 Garante ambiente de sessão e banco limpo
        $this->startSession();

        $rawPrefix = env('TEST_PREFIX', 'master.');
        $cleanPrefix = preg_replace('/[^a-zA-Z0-9_]+/', '', $rawPrefix);
        $this->prefix = $cleanPrefix . '.';

        // 🚀 Opcional: rodar seeders (mantém super master e escola base)
        $this->artisan('db:seed');
    }

    // ============================================================
    // 🏫 SEÇÃO 1 — ESCOLA MASTER
    // ============================================================

    /** @test */
    public function escola_master_nao_pode_ser_excluida()
    {
        $escolaMaster = Escola::factory()->create(['is_master' => true]);
        $superMaster  = Usuario::factory()->create(['is_super_master' => true]);

        $this->actingAs($superMaster);

        $response = $this->delete(route($this->prefix . 'escolas.destroy', $escolaMaster->id));

        $response->assertRedirect();
        $this->assertDatabaseHas(prefix('escola'), ['id' => $escolaMaster->id]);
    }

    /** @test */
    public function master_comum_nao_pode_editar_ou_excluir_escola_master()
    {
        $escolaMaster = Escola::factory()->create(['is_master' => true]);
        $masterComum  = Usuario::factory()->create(['is_super_master' => false]);

        $this->actingAs($masterComum);

        // 🔸 Edit bloqueado
        $edit = $this->get(route($this->prefix . 'escolas.edit', $escolaMaster->id));
        $edit->assertRedirect();
        $this->assertDatabaseHas(prefix('escola'), ['id' => $escolaMaster->id]);

        // 🔸 Destroy bloqueado
        $destroy = $this->delete(route($this->prefix . 'escolas.destroy', $escolaMaster->id));
        $destroy->assertRedirect();
        $this->assertDatabaseHas(prefix('escola'), ['id' => $escolaMaster->id]);
    }

    /** @test */
    public function super_master_pode_editar_escola_master()
    {
        $escolaMaster = Escola::factory()->create(['is_master' => true]);
        $superMaster  = Usuario::factory()->create(['is_super_master' => true]);

        $this->actingAs($superMaster);

        $response = $this->get(route($this->prefix . 'escolas.edit', $escolaMaster->id));

        $response->assertStatus(200);
        $response->assertViewIs('master.escolas.edit');
    }

    /** @test */
    public function view_nao_exibe_botao_excluir_para_escola_master()
    {
        $escolaMaster = Escola::factory()->create(['is_master' => true]);
        $superMaster  = Usuario::factory()->create(['is_super_master' => true]);

        $this->actingAs($superMaster);

        $response = $this->get(route($this->prefix . 'escolas.index'));
        $response->assertDontSee('Excluir');
    }

    // ============================================================
    // 👤 SEÇÃO 2 — USUÁRIO SUPER MASTER
    // ============================================================

    /** @test */
    public function usuario_super_master_nao_pode_ser_excluido()
    {
        $usuarioMaster = Usuario::factory()->create(['is_super_master' => true]);
        $superMaster   = Usuario::factory()->create(['is_super_master' => true]);

        $this->actingAs($superMaster);

        $response = $this->delete(route($this->prefix . 'usuarios.destroy', $usuarioMaster->id));

        $response->assertRedirect();
        $this->assertDatabaseHas(prefix('usuario'), [
            'id' => $usuarioMaster->id,
            'is_super_master' => true,
        ]);
    }

    /** @test */
    public function usuario_nao_pode_excluir_a_si_mesmo()
    {
        $usuario = Usuario::factory()->create(['is_super_master' => false]);
        $this->actingAs($usuario);

        $response = $this->delete(route($this->prefix . 'usuarios.destroy', $usuario->id));

        $response->assertRedirect();
        $this->assertDatabaseHas(prefix('usuario'), ['id' => $usuario->id]);
    }

    /** @test */
    public function view_nao_exibe_botao_excluir_para_usuario_super_master()
    {
        $usuarioMaster = Usuario::factory()->create(['is_super_master' => true]);
        $superMaster   = Usuario::factory()->create(['is_super_master' => true]);

        $this->actingAs($superMaster);

        $response = $this->get(route($this->prefix . 'usuarios.index'));
        $response->assertDontSee('Excluir');
    }

    /** @test */
    public function master_comum_nao_pode_excluir_ou_editar_super_master()
    {
        $superMaster  = Usuario::factory()->create(['is_super_master' => true]);
        $masterComum  = Usuario::factory()->create(['is_super_master' => false]);

        $this->actingAs($masterComum);

        // Tenta editar
        $edit = $this->get(route($this->prefix . 'usuarios.edit', $superMaster->id));
        $edit->assertRedirect();

        // Tenta excluir
        $destroy = $this->delete(route($this->prefix . 'usuarios.destroy', $superMaster->id));
        $destroy->assertRedirect();

        $this->assertDatabaseHas(prefix('usuario'), ['id' => $superMaster->id]);
    }
}
