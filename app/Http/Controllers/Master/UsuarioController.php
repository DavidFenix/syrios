<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $filtro = request('tipo');
        $usuarios = Usuario::with(['escola','roles'])->filtrarPorEscola($filtro)->get();

        //$usuarios = Usuario::with(['escola', 'roles'])->get();
        return view('master.usuarios.index', compact('usuarios','filtro'));
    }

    public function create()
    {
        $escolas = Escola::all();
        $roles   = Role::all();
        return view('master.usuarios.create', compact('escolas', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_u'    => 'required|string|max:100',
            'cpf'       => 'required|string|max:20',
            'school_id' => 'required|integer',
        ]);

        // Verifica se o CPF já existe
        $usuarioExistente = Usuario::where('cpf', $request->cpf)->first();

        if ($usuarioExistente) {
            // Retorna para a view com flag de "usuário já existente"
            return redirect()
                ->back()
                ->withInput()
                ->with('usuario_existente', $usuarioExistente->id);
        }

        // ✅ Criação de novo usuário
        $request->validate([
            'senha' => 'required|string|min:6',
        ]);

        $usuario = Usuario::create([
            'nome_u'     => $request->nome_u,
            'cpf'        => $request->cpf,
            'senha_hash' => Hash::make($request->senha),
            'status'     => 1,
            'school_id'  => $request->school_id,
        ]);

        // 🔗 Vincula roles (com school_id)
        if ($request->filled('roles')) {
            foreach ($request->roles as $role_id) {
                $usuario->roles()->attach($role_id, ['school_id' => $request->school_id]);
            }
        }

        return redirect()
            ->route('master.usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }
    
    public function vincular(Request $request, $usuarioId)
    {
        $usuario = Usuario::findOrFail($usuarioId);

        $request->validate([
            'school_id' => 'required|integer',
            'roles'     => 'array|required'
        ]);

        foreach ($request->roles as $roleId) {
            $jaTem = $usuario->roles()
                ->where('role_id', $roleId)
                ->wherePivot('school_id', $request->school_id)
                ->exists();

            if (!$jaTem) {
                $usuario->roles()->attach($roleId, ['school_id' => $request->school_id]);
            }
        }

        return redirect()
            ->route('master.usuarios.index')
            ->with('success', 'Usuário existente vinculado à escola selecionada!');
    }


    public function edit(Usuario $usuario)
    {
        
        $auth = auth()->user();

        // 🔒 Proteção 1: regra:impede edição do Super Master por qualquer um que não seja o Super Master
        if ($usuario->is_super_master && !$auth->is_super_master) {
            return redirect()
                ->route('master.usuarios.index')
                ->with('error', 'Você não tem permissão para editar o usuário Super Master.');
        }

        // 🔒 Proteção 2: regra:impede que um Master comum edite outro Master
        if ($usuario->roles->pluck('role_name')->contains('master') && !$auth->is_super_master) {
            return redirect()
                ->route('master.usuarios.index')
                ->with('error', 'Apenas o Super Master pode editar outro usuário Master.');
        }

        // 🔒 Proteção 3: regra:impede que o próprio Super Master seja editado por outro Super Master (opcional)
        // se quiser permitir edição apenas dele mesmo, ative esta verificação:
        if ($auth->is_super_master && $usuario->is_super_master && $auth->id !== $usuario->id) {
            return redirect()
                ->route('master.usuarios.index')
                ->with('error', 'Um Super Master não pode editar outro Super Master.');
        }
        

        // 🔒 regra:Impede edição de super master por quem não for super master
        // if ($usuario->is_super_master && (!$auth || !$auth->is_super_master)) {
        //     return redirect()
        //         ->route('master.usuarios.index')
        //         ->with('error', 'A conta Super Master só pode ser editada pelo próprio Super Master.');
        // }

        $escolas = Escola::all();
        $roles   = Role::all();
        $rolesUsuario = $usuario->roles->pluck('id')->toArray();

        return view('master.usuarios.edit', compact('usuario', 'escolas', 'roles', 'rolesUsuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $auth = auth()->user();

        //🔒 regra:Impede atualização do super master por quem não for super master
        if ($usuario->is_super_master && !$auth->is_super_master) {
            return redirect()
                ->route('master.usuarios.index')
                ->with('error', 'Você não tem permissão para editar o usuário Super Master.');
        }

        //🔒 regra:Impede atualização do super master por quem não for super master
        if ($usuario->roles->pluck('role_name')->contains('master') && !$auth->is_super_master) {
            return redirect()
                ->route('master.usuarios.index')
                ->with('error', 'Apenas o Super Master pode editar outro usuário Master.');
        }

        // 🔒 regra:Impede atualização do super master por quem não for super master
        // if ($usuario->is_super_master && (!$auth || !$auth->is_super_master)) {
        //     return redirect()
        //         ->route('master.usuarios.index')
        //         ->with('error', 'A conta Super Master só pode ser alterada pelo próprio Super Master.');
        // }

        $request->validate([
            'nome_u'    => 'required|string|max:100',
            'cpf'       => 'required|string|max:20',
            'school_id' => 'required|integer',
            'status'    => 'required|in:0,1',
        ]);

        // Atualiza dados básicos
        $usuario->update([
            'nome_u'    => $request->nome_u,
            'cpf'       => $request->cpf,
            'status'    => $request->status,
            'school_id' => $request->school_id,
        ]);

        // Atualiza senha, se informada
        if ($request->filled('senha')) {
            $usuario->update(['senha_hash' => Hash::make($request->senha)]);
        }

        return redirect()
            ->route('master.usuarios.index')
            ->with('success', "Usuário Atualizado.");
    }

    public function editRoles(Request $request, Usuario $usuario)
    {
        $escolas = Escola::all();
        $roles   = Role::all();

        $schoolIdSelecionada = $request->input('school_id');

        // se ainda não escolheu, não carrega roles
        $rolesSelecionadas = [];
        if ($schoolIdSelecionada) {
            $rolesSelecionadas = $usuario->roles()
                ->wherePivot('school_id', $schoolIdSelecionada)
                ->pluck('syrios_role.id')
                ->toArray();
        }

        return view('master.usuarios.roles', compact(
            'usuario', 'escolas', 'roles', 'schoolIdSelecionada', 'rolesSelecionadas'
        ));
    }

    public function updateRoles(Request $request, Usuario $usuario)
    {
        $request->validate([
            'school_id' => 'required|integer',
            'roles'     => 'array'
        ]);

        $schoolId = $request->school_id;
        $novasRoles = $request->input('roles', []);

        // 🔍 Busca vínculos antigos
        $vinculosAntigos = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->pluck('syrios_role.id')
            ->toArray();

        $paraAdicionar = array_diff($novasRoles, $vinculosAntigos);
        $paraRemover   = array_diff($vinculosAntigos, $novasRoles);

        // 🔒 regra:Impede remover a role master do usuario super_master
        if ($usuario->is_super_master) {
            // Descobre qual é o ID da role "master" no banco
            $roleMasterId = \App\Models\SyriosRole::where('role_name', 'master')->value('id');

            if ($roleMasterId && in_array($roleMasterId, $paraRemover)) {
                // Remove o ID da role master da lista de remoção
                $paraRemover = array_diff($paraRemover, [$roleMasterId]);

                // Mensagem de aviso
                session()->flash('warning', 'A role "master" não pode ser removida do usuário da escola principal.');
            }
        }

        // Adiciona novas roles
        foreach ($paraAdicionar as $roleId) {
            try {
                $usuario->roles()->attach($roleId, ['school_id' => $schoolId]);
            } catch (\Exception $e) {
                return back()->with('error', "Não foi possível adicionar a role (ID $roleId): {$e->getMessage()}");
            }
        }

        // Remove antigas (exceto master da escola 1)
        foreach ($paraRemover as $roleId) {
            try {
                $usuario->roles()->wherePivot('school_id', $schoolId)->detach($roleId);
            } catch (\Exception $e) {
                return back()->with('error', "Não foi possível remover a role (ID $roleId): {$e->getMessage()}");
            }
        }

        // Retorna com sucesso
        return back()->with('success', 'Roles atualizadas com sucesso!');
    }

    public function confirmDestroy(Usuario $usuario)
    {
        // ⚙️ Coleta vínculos diretos que impedem exclusão
        $vinculos = [
            'professor'   => \DB::table('syrios_professor')->where('usuario_id', $usuario->id)->count(),
            'notificacao' => \DB::table('syrios_notificacao')->where('usuario_id', $usuario->id)->count(),
            'sessao'      => \DB::table('syrios_sessao')->where('usuario_id', $usuario->id)->count(),
            'roles'       => \DB::table('syrios_usuario_role')->where('usuario_id', $usuario->id)->count(),
        ];

        // 🏫 Lista de escolas vinculadas (por roles e/ou professor)
        $escolasRoles = \DB::table('syrios_usuario_role as ur')
            ->join('syrios_escola as e', 'e.id', '=', 'ur.school_id')
            ->where('ur.usuario_id', $usuario->id)
            ->select('e.id', 'e.nome_e', 'e.is_master')
            ->distinct();

        $escolasProfessor = \DB::table('syrios_professor as p')
            ->join('syrios_escola as e', 'e.id', '=', 'p.school_id')
            ->where('p.usuario_id', $usuario->id)
            ->select('e.id', 'e.nome_e', 'e.is_master')
            ->distinct();

        // Une os resultados das duas fontes e remove duplicatas
        $escolasVinculadas = $escolasRoles
            ->union($escolasProfessor)
            ->get();


        return view('master.usuarios.confirm_destroy', compact('usuario', 'vinculos', 'escolasVinculadas'));
    }

    public function destroy(Usuario $usuario)
    {
        
        $auth = auth()->user();

        // 🚫 regra:Impede excluir a si mesmo
        if ($auth && $auth->id === $usuario->id) {
            return redirect()
                ->route('master.usuarios.index')
                ->with('error', 'Você não pode excluir sua própria conta.');
        }

        // 🔒 regra:Impede excluir o Super Master (a menos que seja o próprio super_master)
        if ($usuario->is_super_master && !$auth->is_super_master) {
            return redirect()
                ->route('master.usuarios.index')
                ->with('error', 'Apenas o Super Master pode excluir outro Super Master.');
        }

        // 🔒 Impede que um Master comum exclua outro Master
        if ($usuario->roles->pluck('role_name')->contains('master') && !$auth->is_super_master) {
            return redirect()
                ->route('master.usuarios.index')
                ->with('error', 'Apenas o Super Master pode excluir outro usuário Master.');
        }

        // if ($usuario->is_super_master) {
        //     return redirect()
        //         ->route('master.usuarios.index')
        //         ->with('error', 'O usuário master não pode ser excluído.');
        // }


        try {
            // Remove vínculos da pivot
            $usuario->roles()->detach();

            $usuario->delete();

            return redirect()->route('master.usuarios.index')
                ->with('success', 'Usuário excluído com sucesso!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->with('error', 'Não foi possível excluir o usuário. Existem registros vinculados.');
            }

            return redirect()->back()
                ->with('error', 'Erro ao excluir: ' . $e->getMessage());
        }
    }

}



