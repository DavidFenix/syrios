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
        $escolas = Escola::all();
        $roles   = Role::all();
        $rolesUsuario = $usuario->roles->pluck('id')->toArray();

        return view('master.usuarios.edit', compact('usuario', 'escolas', 'roles', 'rolesUsuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
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

        /* desisto de fazer isso aqui...nem a ia conseguiu desse jeito!!!
            // ⚙️ Atualização de roles por múltiplas escolas
            $rolesMarcadas = collect($request->input('roles', []))->map(fn($r) => (int)$r)->toArray();
            $rolesAtuais = $usuario->roles()->get(['syrios_role.id', 'syrios_usuario_role.school_id']);

            $adicionados = 0;
            $removidos   = 0;

            // Percorre todas as escolas existentes nas roles atuais
            $todasEscolas = $rolesAtuais->pluck('school_id')->unique()->values();

            // Inclui também a escola principal selecionada (caso seja nova)
            if (!$todasEscolas->contains($request->school_id)) {
                $todasEscolas->push($request->school_id);
            }

            foreach ($todasEscolas as $schoolId) {
                $rolesAntigas = $rolesAtuais
                    ->where('school_id', $schoolId)
                    ->pluck('id')
                    ->toArray();

                $paraAdicionar = array_diff($rolesMarcadas, $rolesAntigas);
                $paraRemover   = array_diff($rolesAntigas, $rolesMarcadas);

                // Adiciona novos vínculos
                foreach ($paraAdicionar as $roleId) {
                    $usuario->roles()->attach($roleId, ['school_id' => $schoolId]);
                    $adicionados++;
                }

                // Remove vínculos desmarcados
                if (!empty($paraRemover)) {
                    $usuario->roles()
                        ->wherePivot('school_id', $schoolId)
                        ->detach($paraRemover);
                    $removidos += count($paraRemover);
                }
            }

            // 🟢 Mensagem de retorno contextual
            $msg = "Usuário atualizado com sucesso!";
            if ($adicionados > 0 || $removidos > 0) {
                $msg .= " ($adicionados role(s) adicionada(s), $removidos removida(s)).";
            }
            */
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

    /*public function updateRoles(Request $request, Usuario $usuario)
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

        // Adiciona novas
        foreach ($paraAdicionar as $roleId) {
            try {
                $usuario->roles()->attach($roleId, ['school_id' => $schoolId]);
            } catch (\Exception $e) {
                return back()->with('error', "Não foi possível adicionar a role (ID $roleId): {$e->getMessage()}");
            }
        }

        // Remove antigas
        foreach ($paraRemover as $roleId) {
            try {
                $usuario->roles()->wherePivot('school_id', $schoolId)->detach($roleId);
            } catch (\Exception $e) {
                return back()->with('error', "Não foi possível remover a role (ID $roleId): {$e->getMessage()}");
            }
        }

        return back()->with('success', 'Roles atualizadas com sucesso!');
    }*/


    /*public function update(Request $request, Usuario $usuario)
    {
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

        // Atualização dos papéis (roles)
        $novaSchoolId = $request->school_id;
        $novasRoles = $request->input('roles', []);

        // 🔍 Busca vínculos antigos do usuário nesta mesma escola
        $vinculosAntigos = $usuario->roles()
            ->wherePivot('school_id', $novaSchoolId)
            ->pluck('syrios_role.id')
            ->toArray();

        // Roles a adicionar
        $paraAdicionar = array_diff($novasRoles, $vinculosAntigos);
        // Roles a remover
        $paraRemover = array_diff($vinculosAntigos, $novasRoles);

        // 🔗 Adiciona novos vínculos
        foreach ($paraAdicionar as $roleId) {
            $usuario->roles()->attach($roleId, ['school_id' => $novaSchoolId]);
        }

        // ❌ Remove vínculos que foram desmarcados
        if (!empty($paraRemover)) {
            $usuario->roles()
                ->wherePivot('school_id', $novaSchoolId)
                ->detach($paraRemover);
        }

        return redirect()
            ->route('master.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }*/


    /*public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nome_u'   => 'required|string|max:100',
            'cpf'      => 'required|string|max:20',
            'school_id'=> 'required|integer',
            'status'   => 'required|in:0,1',
        ]);

        $usuario->update([
            'nome_u'   => $request->nome_u,
            'cpf'      => $request->cpf,
            'status'   => $request->status,
            'school_id'=> $request->school_id,
        ]);

        if ($request->filled('senha')) {
            $usuario->update(['senha_hash' => Hash::make($request->senha)]);
        }

        // Atualizar roles com school_id
        $rolesSync = [];
        if ($request->has('roles')) {
            foreach ($request->roles as $role_id) {
                $rolesSync[$role_id] = ['school_id' => $request->school_id];
            }
        }
        $usuario->roles()->sync($rolesSync);

        return redirect()->route('master.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }*/

    /*
    public function destroy(Usuario $usuario)
    {
        // 🔒 Regra: impede exclusão do Super Master
        if ($usuario->is_super_master) {
            return redirect()->back()
                ->with('error', 'Usuários Super Master não podem ser excluídos.');
        }

        try {
            // Remove vínculos na pivot (roles)
            $usuario->roles()->detach();

            // Exclui o usuário
            $usuario->delete();

            return redirect()->route('master.usuarios.index')
                ->with('success', 'Usuário excluído com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            
            // Captura violação de chave estrangeira
            if ($e->getCode() === '23000') {
                return redirect()->back()->with(
                    'error',
                    'Não foi possível excluir o usuário porque há registros vinculados a ele (violação de integridade referencial).'
                );
            }

            // Outras falhas de banco
            return redirect()->back()->with(
                'error',
                'Ocorreu um erro ao tentar excluir o usuário: ' . $e->getMessage()
            );

        } catch (\Exception $e) {
            // Qualquer outra exceção inesperada
            return redirect()->back()->with(
                'error',
                'Erro inesperado: ' . $e->getMessage()
            );
        }
    }*/

    public function confirmDestroy(Usuario $usuario)
    {
        // ⚙️ Coleta vínculos diretos que impedem exclusão
        $vinculos = [
            'professor'   => \DB::table('syrios_professor')->where('usuario_id', $usuario->id)->count(),
            'notificacao' => \DB::table('syrios_notificacao')->where('usuario_id', $usuario->id)->count(),
            'sessao'      => \DB::table('syrios_sessao')->where('usuario_id', $usuario->id)->count(),
            'roles'       => \DB::table('syrios_usuario_role')->where('usuario_id', $usuario->id)->count(),
        ];

        // 🏫 Lista de escolas vinculadas
        // $escolasVinculadas = \DB::table('syrios_usuario_role as ur')
        //     ->join('syrios_escola as e', 'e.id', '=', 'ur.school_id')
        //     ->where('ur.usuario_id', $usuario->id)
        //     ->select('e.id', 'e.nome_e', 'e.is_master')
        //     ->distinct()
        //     ->get();

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
        if ($usuario->is_super_master) {
            return redirect()->back()
                ->with('error', 'Usuários Super Master não podem ser excluídos.');
        }

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




    /*
    public function destroy(Usuario $usuario)
    {
        // 🔒 regra:Impede excluir usuários da escola master
        if ($usuario->is_super_master) {
            return redirect()->back()
                ->with('error', 'Usuários Super Master não podem ser excluídos.');
        }

        // Remove vínculos na pivot (roles)
        $usuario->roles()->detach();

        // Exclui o usuário
        $usuario->delete();

        return redirect()->route('master.usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }*/


    /*public function destroy(Usuario $usuario)
    {
        // Primeiro remove vínculos na pivot
        $usuario->roles()->detach();

        // Depois exclui usuário
        $usuario->delete();

        return redirect()->route('master.usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }*/

}





/*
    public function store(Request $request)
    {
        $request->validate([
            'nome_u'   => 'required|string|max:100',
            'cpf'      => 'required|string|max:20',
            'senha'    => 'required|string|min:6',
            'school_id'=> 'required|integer',
        ]);

        $usuario = Usuario::create([
            'nome_u'     => $request->nome_u,
            'cpf'        => $request->cpf,
            'senha_hash' => Hash::make($request->senha),
            'status'     => 1,
            'school_id'  => $request->school_id,
        ]);

        // Sincronizar roles com school_id
        $rolesSync = [];
        if ($request->has('roles')) {
            foreach ($request->roles as $role_id) {
                $rolesSync[$role_id] = ['school_id' => $request->school_id];
            }
        }
        $usuario->roles()->sync($rolesSync);

        return redirect()->route('master.usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }
    */


