<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Escola;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MasterFullProtectionBehaviorTest extends TestCase
{
    use RefreshDatabase;

    protected string $prefix;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 🧠 Garante que os testes tenham uma sessão ativa
        $this->startSession();

        // 🔹 Recria a base completa de desenvolvimento dentro do banco de teste
        $this->artisan('db:seed', ['--class' => 'FullDevSeeder']);

        // Lê o prefixo e remove todos os espaços e pontos extras antes/depois
        $rawPrefix = env('TEST_PREFIX', 'master.');
        
        $cleanPrefix = preg_replace('/[^a-zA-Z0-9_]+/', '', $rawPrefix); // mantém apenas letras, números e "_"
        
        $this->prefix = $cleanPrefix . '.';

    }

    /** @test */
    // public function escola_master_nao_pode_ser_excluida()
    // {
    //     // 🏫 Cria escola master
    //     $escolaMaster = Escola::factory()->create(['is_master' => true]);

    //     // 🔥 Tenta excluir
    //     $response = $this->delete(route($this->prefix . 'escolas.destroy', $escolaMaster->id));

    //     // ✅ Deve redirecionar (bloqueada)
    //     $response->assertRedirect();

    //     // ✅ Escola continua existindo
    //     $this->assertDatabaseHas(prefix('escola'), [
    //         'id' => $escolaMaster->id,
    //         'is_master' => true
    //     ]);
    // }

    /** @test */
    public function usuario_super_master_nao_pode_ser_excluido()
    {
        // 👤 Cria usuário super master
        $usuarioMaster = Usuario::factory()->create(['is_super_master' => true]);

        // 🔥 Tenta excluir
        $response = $this->delete(route($this->prefix . 'usuarios.destroy', $usuarioMaster->id));

        // ✅ Deve redirecionar (bloqueada)
        $response->assertRedirect();

        // ✅ Usuário ainda existe no banco
        $this->assertDatabaseHas(prefix('usuario'), [
            'id' => $usuarioMaster->id,
            'is_super_master' => true
        ]);
    }

    /** @test */
    public function view_nao_exibe_botao_excluir_para_escola_master()
    {
        $escolaMaster = Escola::factory()->create(['is_master' => true]);
        $response = $this->get(route($this->prefix . 'escolas.index'));

        // ✅ O botão "Excluir" não aparece
        $response->assertDontSee('Excluir');
    }

    /** @test */
    public function view_nao_exibe_botao_excluir_para_usuario_super_master()
    {
        $usuarioMaster = Usuario::factory()->create(['is_super_master' => true]);
        $response = $this->get(route($this->prefix . 'usuarios.index'));

        // ✅ O botão "Excluir" não aparece
        $response->assertDontSee('Excluir');
    }

    //--------------------------------------------------------------------------

     /** @test */
    public function escola_master_nao_pode_ser_excluida()
    {
        // 🏫 Cria uma escola master e um super_master autenticado
        $escolaMaster = Escola::factory()->create(['is_master' => true]);
        $superMaster = Usuario::factory()->create(['is_super_master' => true]);

        $this->actingAs($superMaster);

        // 🔸 Tenta excluir a escola master
        $response = $this->delete(route('master.escolas.destroy', $escolaMaster->id));

        // ✅ Deve redirecionar e a escola continuar existindo
        $response->assertRedirect();
        $this->assertDatabaseHas(prefix('escola'), ['id' => $escolaMaster->id]);
    }

    /** @test */
    public function master_comum_nao_pode_editar_ou_excluir_escola_master()
    {
        $escolaMaster = Escola::factory()->create(['is_master' => true]);

        $masterComum = Usuario::factory()->create(['is_super_master' => false]);
        $masterComum->roles()->attach(1, ['school_id' => $escolaMaster->id]); // role master genérica

        $this->actingAs($masterComum);

        // 🔸 Tenta acessar o edit
        $edit = $this->get(route('master.escolas.edit', $escolaMaster->id));
        $edit->assertRedirect();
        $this->assertDatabaseHas(prefix('escola'), ['id' => $escolaMaster->id]);

        // 🔸 Tenta excluir
        $destroy = $this->delete(route('master.escolas.destroy', $escolaMaster->id));
        $destroy->assertRedirect();
        $this->assertDatabaseHas(prefix('escola'), ['id' => $escolaMaster->id]);
    }

    /** @test */
    public function super_master_pode_editar_escola_master()
    {
        $escolaMaster = Escola::factory()->create(['is_master' => true]);
        $superMaster = Usuario::factory()->create(['is_super_master' => true]);

        $this->actingAs($superMaster);

        $response = $this->get(route('master.escolas.edit', $escolaMaster->id));

        // ✅ Super Master deve conseguir acessar
        $response->assertStatus(200);
        $response->assertViewIs('master.escolas.edit');
    }
}
