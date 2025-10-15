<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Throwable;


class ProfessorController extends Controller
{
    
    public function index()
    {
        $schoolId = (int) session('current_school_id');

        // 🔹 Role "professor"
        $roleProfessorId = Role::where('role_name', 'professor')->value('id');

        // 🔹 Busca todos os usuários com role "professor" nesta escola
        $usuariosComRole = Usuario::whereHas('roles', function($q) use ($roleProfessorId, $schoolId) {
            $q->where(prefix('usuario_role').'.role_id', $roleProfessorId)
              ->where(prefix('usuario_role').'.school_id', $schoolId);
        })->get();

        // 🔹 Lista de IDs com role (deve estar em syrios_professor)
        $idsComRole = $usuariosComRole->pluck('id')->toArray();

        // 🔹 Lista de professores atualmente registrados na escola
        $professoresExistentes = Professor::where('school_id', $schoolId)->get();

        $idsAtuais = $professoresExistentes->pluck('usuario_id')->toArray();

        $sincronizados = 0;
        $removidos = 0;

        DB::beginTransaction();
        try {
            // ✅ 1) Adicionar professores faltantes
            foreach ($usuariosComRole as $usuario) {
                if (!in_array($usuario->id, $idsAtuais)) {
                    Professor::create([
                        'usuario_id' => $usuario->id,
                        'school_id'  => $schoolId,
                    ]);
                    $sincronizados++;
                }
            }

            // ✅ 2) Remover professores que perderam a role "professor"
            foreach ($professoresExistentes as $professor) {
                if (!in_array($professor->usuario_id, $idsComRole)) {
                    // Verifica dependências (turmas, ocorrências etc.)
                    $temDependencias = DB::table(prefix('oferta'))
                            ->where('professor_id', $professor->id)->exists()
                        || DB::table(prefix('diretor_turma'))
                            ->where('professor_id', $professor->id)->exists()
                        || DB::table(prefix('ocorrencia'))
                            ->where('professor_id', $professor->id)->exists();

                    if ($temDependencias) {
                        continue; // ❗ Pula a exclusão se houver vínculos
                    }

                    $professor->delete();
                    $removidos++;
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao sincronizar professores: '.$e->getMessage());
        }

        // 🔹 Carrega professores com suas escolas e usuários
        $professores = Professor::with(['usuario.escola'])
            ->where('school_id', $schoolId)
            ->get();

        // 🔹 Mensagens de resultado
        $mensagens = [];
        if ($sincronizados > 0) {
            $mensagens[] = "✅ $sincronizados novo(s) professor(es) sincronizado(s).";
        }
        if ($removidos > 0) {
            $mensagens[] = "🗑 $removidos professor(es) removido(s) por perda de vínculo.";
        }

        $mensagem = !empty($mensagens)
            ? implode(' ', $mensagens)
            : 'Lista Atualizada!';

        return view('escola.professores.index', compact('professores', 'mensagem'));
    }


    // public function index()
    // {
    //     $schoolId = session('current_school_id');

    //     // 🔹 Role professor
    //     $roleProfessorId = Role::where('role_name', 'professor')->first()->id;

    //     // 🔹 Busca todos os usuários com role professor nesta escola
    //     $usuariosComRole = Usuario::whereHas('roles', function($q) use ($roleProfessorId, $schoolId) {
    //         $q->where('syrios_usuario_role.role_id', $roleProfessorId)
    //           ->where('syrios_usuario_role.school_id', $schoolId);
    //     })->get();


    //     // 🔹 Conta quantos estavam faltando
    //     $sincronizados = 0;

    //     foreach ($usuariosComRole as $usuario) {
    //         $criado = Professor::firstOrCreate([
    //             'usuario_id' => $usuario->id,
    //             'school_id'  => $schoolId
    //         ]);

    //         if ($criado->wasRecentlyCreated) {
    //             $sincronizados++;
    //         }
    //     }

    //     // 🔹 Carrega professores com suas escolas de origem
    //     $professores = Professor::with(['usuario.escola'])
    //         ->where('school_id', $schoolId)
    //         ->get();

    //     $mensagem = $sincronizados > 0 
    //         ? "Sincronização automática: $sincronizados professor(es) adicionados."
    //         : null;

    //     return view('escola.professores.index', compact('professores', 'mensagem'));
    // }

    public function create()
    {
        return view('escola.professores.create');
    }

    public function store(Request $request)
    {
        $schoolId = session('current_school_id');

        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        Professor::create([
            'usuario_id' => $request->usuario_id,
            'school_id'  => $schoolId,
        ]);

        return redirect()->route('escola.professores.index')->with('success', 'Professor criado!');
    }

    public function edit(Professor $professor)
    {
        return view('escola.professores.edit', compact('professor'));
    }

    public function update(Request $request, Professor $professor)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        $professor->update($request->only('usuario_id'));

        return redirect()->route('escola.professores.index')->with('success', 'Professor atualizado!');
    }

    public function destroy($id)
{
    $schoolId = (int) session('current_school_id');
    $auth     = auth()->user();

    try {
        DB::beginTransaction();

        $professor = Professor::where('school_id', $schoolId)
            ->where('id', $id)
            ->firstOrFail();

        $usuarioId = (int) $professor->usuario_id;
        $isSelf = ($auth->id === $usuarioId);

        // Confirma se gestor tem permissão
        $authTemRoleEscola = $auth->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        if (!$authTemRoleEscola) {
            return back()->with('error', 'Apenas gestores escolares podem remover professores.');
        }

        // Verifica dependências
        $temDependencias = DB::table(prefix('oferta'))
                ->where('professor_id', $professor->id)->exists()
            || DB::table(prefix('diretor_turma'))
                ->where('professor_id', $professor->id)->exists()
            || DB::table(prefix('ocorrencia'))
                ->where('professor_id', $professor->id)->exists();

        if ($temDependencias) {
            DB::rollBack();
            return back()->with('error', 'Não é possível remover este professor: há vínculos ativos.');
        }

        // Remove vínculo da pivot syrios_usuario_role (assim não será recriado)
        $roleProfessorId = Role::where('role_name', 'professor')->value('id');
        if ($roleProfessorId) {
            DB::table(prefix('usuario_role'))
                ->where('usuario_id', $usuarioId)
                ->where('school_id', $schoolId)
                ->where('role_id', $roleProfessorId)
                ->delete();
        }

        // Exclui registro da tabela professor
        $professor->delete();

        DB::commit();

        $mensagem = $isSelf
            ? 'Você se removeu da lista de professores desta escola.'
            : 'Professor removido com sucesso.';

        return redirect()->route('escola.professores.index')->with('success', $mensagem);

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Erro ao excluir: '.$e->getMessage());
    }
}



    // public function destroy($id)
    // {
    //     $schoolId = (int) session('current_school_id');

    //     try {
    //         DB::beginTransaction();

    //         // 🔒 Busca professor apenas da escola atual
    //         $professor = Professor::where('school_id', $schoolId)
    //             ->where('id', $id)
    //             ->firstOrFail();

    //         $usuarioId = (int) $professor->usuario_id;

    //         // 🔍 Segurança 1: verifica dependências (ex: turmas, ofertas, ocorrências)
    //         $temDependencias = DB::table(prefix('oferta'))
    //             ->where('professor_id', $professor->id)
    //             ->exists()
    //             || DB::table(prefix('diretor_turma'))
    //                 ->where('professor_id', $professor->id)
    //                 ->exists()
    //             || DB::table(prefix('ocorrencia'))
    //                 ->where('professor_id', $professor->id)
    //                 ->exists();

    //         if ($temDependencias) {
    //             return back()->with('error', 'Não é possível excluir este professor: ele possui vínculos com turmas, ofertas ou ocorrências.');
    //         }

    //         // 🔒 Segurança 2: protege o próprio usuário logado (não se excluir)
    //         if ($usuarioId === auth()->id()) {
    //             return back()->with('error', 'Você não pode excluir a si mesmo.');
    //         }

    //         // 🔹 Remove o vínculo na tabela pivot
    //         $roleProfessorId = Role::where('role_name', 'professor')->value('id');

    //         if ($roleProfessorId) {
    //             DB::table(prefix('usuario_role'))
    //                 ->where('usuario_id', $usuarioId)
    //                 ->where('school_id', $schoolId)
    //                 ->where('role_id', $roleProfessorId)
    //                 ->delete();
    //         }

    //         // 🔹 Remove o registro de professor
    //         $professor->delete();

    //         DB::commit();
    //         return redirect()
    //             ->route('escola.professores.index')
    //             ->with('success', 'Professor removido com sucesso.');

    //     } catch (QueryException $e) {
    //         DB::rollBack();

    //         // ⚠️ Erro de integridade (FK constraint)
    //         if (str_contains($e->getMessage(), 'foreign key')) {
    //             return back()->with('error', 'Não foi possível excluir: existem registros vinculados (FK constraint).');
    //         }

    //         // ⚙️ Outros erros SQL
    //         return back()->with('error', 'Erro ao excluir o professor: ' . $e->getMessage());

    //     } catch (Throwable $e) {
    //         DB::rollBack();
    //         // 🧠 Tratamento genérico para erros inesperados
    //         return back()->with('error', 'Erro inesperado: ' . $e->getMessage());
    //     }
    // }

    // public function destroy($id)
        // {
        //     $schoolId = session('current_school_id');
        //     $professor = Professor::where('school_id', $schoolId)->findOrFail($id);
        //     $professor->delete();

        //     return redirect()->route('escola.professores.index')->with('success','Professor removido!');
        // }

}




/*
namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorController extends Controller
{
    public function index()
    {
        $schoolId = session('current_school_id');
        //$professores = Professor::where('school_id', $schoolId)->with('usuario')->get();

        //Assim, cada $p trará automaticamente o usuário e a escola de origem ($p->usuario->escola).
        $professores = Professor::with(['usuario.escola'])
        ->where('school_id', $schoolId)
        ->get();

        return view('escola.professores.index', compact('professores'));
    }

    // public function index()
    // {
    //     $escola = Auth::user()->escola;
    //     $professores = Professor::where('school_id', $escola->id)->get();

    //     return view('escola.professores.index', compact('professores', 'escola'));
    // }

    public function create()
    {
        return view('escola.professores.create');
    }

    public function store(Request $request)
    {
        $schoolId = session('current_school_id');
        //$escola = Auth::user()->escola;

        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        Professor::create([
            'usuario_id' => $request->usuario_id,
            'school_id'  => $schoolId,
        ]);

        return redirect()->route('escola.professores.index')->with('success', 'Professor criado!');
    }

    public function edit(Professor $professor)
    {
        return view('escola.professores.edit', compact('professor'));
    }

    public function update(Request $request, Professor $professor)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        $professor->update($request->only('usuario_id'));

        return redirect()->route('escola.professores.index')->with('success', 'Professor atualizado!');
    }

    public function destroy($id)
    {
        $schoolId = session('current_school_id');
        $professor = Professor::where('school_id', $schoolId)->findOrFail($id);
        $professor->delete();

        return redirect()->route('escola.professores.index')->with('success','Professor removido!');
    }
    
    // public function destroy(Professor $professor)
    // {
    //     $professor->delete();
    //     return redirect()->route('escola.professores.index')->with('success', 'Professor excluído!');
    // }
}
*/