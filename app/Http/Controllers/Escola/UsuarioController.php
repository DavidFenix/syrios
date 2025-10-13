<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\{Usuario, Role, Professor, Escola};

class UsuarioController extends Controller
{
    public function index()
    {
        $schoolId = session('current_school_id');
        $usuarios = Usuario::whereHas('roles', function($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->get();

        return view('escola.usuarios.index', compact('usuarios'));
    }


    public function create()
    {
        $schoolId = session('current_school_id');

        // 🔒 Filtra roles permitidas (exclui master, secretaria, escola)
        $roles = Role::whereNotIn('role_name', ['master', 'secretaria', 'escola'])->get();

        return view('escola.usuarios.create', compact('roles', 'schoolId'));
    }

    /*🧱 Resumo das proteções
        Cenário / Ação
        Escola tenta criar usuário com role master, secretaria ou escola  /  ❌ Rejeitado com mensagem amigável
        Escola tenta vincular role proibida via POST (manual)  / ❌ Rejeitado
        Interface de criação (create) /  🔒 Já não mostra essas roles
        Inserções duplicadas   / ✅ Prevenidas com insertOrIgnore()
        Roles superiores existentes no usuário / ✅ Mantidas, não removidas
        Role professor / 👨‍🏫 Cria entrada em syrios_professor automaticamente
        */
    public function store(Request $request)
    {
        $schoolId = session('current_school_id'); // contexto da escola logada

        $request->validate([
            'nome_u'   => 'required|string|max:100',
            'cpf'      => 'required|string|max:11',
            'password' => 'required|string|min:6',
            'status'   => 'required|boolean',
            'roles'    => 'required|array'
        ]);

        // 🔒 Protege contra tentativa manual de criar usuários com roles proibidas
        $rolesInvalidas = Role::whereIn('id', $request->roles)
            ->whereIn('role_name', ['master', 'secretaria', 'escola'])
            ->pluck('role_name')
            ->toArray();

        if (!empty($rolesInvalidas)) {
            return back()
                ->withInput()
                ->with('error', '🚫 Não é permitido criar usuário com as roles: ' . implode(', ', $rolesInvalidas));
        }

        // 🔍 Verifica se já existe usuário com o mesmo CPF
        $usuarioExistente = Usuario::where('cpf', $request->cpf)->first();

        if ($usuarioExistente) {
            // Redireciona para vinculação
            return redirect()
                ->back()
                ->withInput()
                ->with('usuario_existente', $usuarioExistente->id);
        }

        // 👤 Cria novo usuário nesta escola
        $usuario = Usuario::create([
            'school_id'  => $schoolId,
            'cpf'        => $request->cpf,
            'senha_hash' => Hash::make($request->password),
            'nome_u'     => $request->nome_u,
            'status'     => $request->status,
        ]);

        // 🔗 Associa roles (apenas as permitidas)
        foreach ($request->roles as $roleId) {
            DB::table(prefix('usuario_role'))->insertOrIgnore([
                'usuario_id' => $usuario->id,
                'role_id'    => $roleId,
                'school_id'  => $schoolId,
            ]);
        }

        // 👨‍🏫 Se for professor → cria também em syrios_professor
        $roleProfessorId = Role::where('role_name', 'professor')->value('id');
        if (in_array($roleProfessorId, $request->roles)) {
            Professor::firstOrCreate([
                'usuario_id' => $usuario->id,
                'school_id'  => $schoolId
            ]);
        }

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function vincular(Request $request, Usuario $usuario)
    {
        $schoolId = session('current_school_id');

        if (!$schoolId) {
            return redirect()->route('escola.usuarios.index')
                ->with('error', 'Nenhuma escola selecionada no contexto.');
        }

        $request->validate([
            'roles' => 'required|array'
        ]);

        // 🔒 Bloqueia tentativa de vincular roles proibidas
        $rolesInvalidas = Role::whereIn('id', $request->roles)
            ->whereIn('role_name', ['master', 'secretaria', 'escola'])
            ->pluck('role_name')
            ->toArray();

        if (!empty($rolesInvalidas)) {
            return back()->with('error', '🚫 Não é permitido vincular as roles: ' . implode(', ', $rolesInvalidas));
        }

        // 🔍 Busca roles já existentes nesta escola
        $rolesExistentes = DB::table(prefix('usuario_role'))
            ->where('usuario_id', $usuario->id)
            ->where('school_id', $schoolId)
            ->pluck('role_id')
            ->toArray();

        // 🔎 Calcula apenas as novas roles (sem duplicar)
        $novasRoles = array_diff($request->roles, $rolesExistentes);

        foreach ($novasRoles as $roleId) {
            DB::table(prefix('usuario_role'))->insertOrIgnore([
                'usuario_id' => $usuario->id,
                'role_id'    => $roleId,
                'school_id'  => $schoolId,
            ]);

            // 👨‍🏫 Se for professor → cria também em syrios_professor
            $roleProfessorId = Role::where('role_name', 'professor')->value('id');
            if ($roleId == $roleProfessorId) {
                Professor::firstOrCreate([
                    'usuario_id' => $usuario->id,
                    'school_id'  => $schoolId
                ]);
            }
        }

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário vinculado à escola com sucesso!');
    }
    


    // public function index()
        // {
        //     //$escolaId = auth()->user()->school_id;
        //     $escolaId = session('current_school_id'); // escola logada

        //     // lista apenas outros usuários da mesma escola
        //     $usuarios = Usuario::where('school_id', $escolaId)
        //         ->where('id', '!=', auth()->id())
        //         ->get();

        //     return view('escola.usuarios.index', compact('usuarios'));
        // }

    /*public function create()
    {
        $roles = Role::whereNotIn('role_name', ['master','secretaria','escola'])->get();
        return view('escola.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $schoolId = session('current_school_id'); // contexto da escola logada

        $request->validate([
            'nome_u'   => 'required|string|max:100',
            'cpf'      => 'required|string|max:11',
            'password' => 'required|string|min:6',
            'status'   => 'required|boolean',
            'roles'    => 'required|array'
        ]);

        // 🔍 Verifica se já existe usuário com o mesmo CPF (em qualquer escola)
        $usuarioExistente = Usuario::where('cpf', $request->cpf)->first();

        if ($usuarioExistente) {
            // ⚠️ Já existe → redireciona para vinculação (sem criar novo)
            return redirect()
                ->back()
                ->withInput()
                ->with('usuario_existente', $usuarioExistente->id);
        }

        // 👤 Cria novo usuário nesta escola
        $usuario = Usuario::create([
            'school_id'  => $schoolId,
            'cpf'        => $request->cpf,
            'senha_hash' => Hash::make($request->password),
            'nome_u'     => $request->nome_u,
            'status'     => $request->status,
        ]);

        // 🎯 Adiciona roles selecionadas, evitando duplicações
        foreach ($request->roles as $roleId) {
            DB::table(prefix('usuario_role'))->insertOrIgnore([
                'usuario_id' => $usuario->id,
                'role_id'    => $roleId,
                'school_id'  => $schoolId,
            ]);
        }

        // 👨‍🏫 Se for professor, cria também em syrios_professor
        $roleProfessorId = Role::where('role_name', 'professor')->value('id');
        if (in_array($roleProfessorId, $request->roles)) {
            Professor::firstOrCreate([
                'usuario_id' => $usuario->id,
                'school_id'  => $schoolId
            ]);
        }

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function vincular(Request $request, $usuarioId)
    {
        $schoolId = session('current_school_id');

        if (!$schoolId) {
            return redirect()->route('escola.usuarios.index')
                ->with('error', 'Nenhuma escola selecionada no contexto.');
        }

        $usuario = Usuario::findOrFail($usuarioId);

        $request->validate([
            'roles' => 'required|array'
        ]);

        // 🔍 Busca roles já existentes nesta escola
        $rolesExistentes = DB::table(prefix('usuario_role'))
            ->where('usuario_id', $usuario->id)
            ->where('school_id', $schoolId)
            ->pluck('role_id')
            ->toArray();

        // 🔎 Calcula apenas as novas roles (sem duplicar)
        $novasRoles = array_diff($request->roles, $rolesExistentes);

        foreach ($novasRoles as $roleId) {
            DB::table(prefix('usuario_role'))->insertOrIgnore([
                'usuario_id' => $usuario->id,
                'role_id'    => $roleId,
                'school_id'  => $schoolId,
            ]);

            // Se for professor → também cria em syrios_professor
            $roleProfessorId = Role::where('role_name', 'professor')->value('id');
            if ($roleId == $roleProfessorId) {
                Professor::firstOrCreate([
                    'usuario_id' => $usuario->id,
                    'school_id'  => $schoolId
                ]);
            }
        }

        // 🚫 NÃO remove roles de outras escolas nem superiores (secretaria/master)

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário vinculado à escola com sucesso!');
    }*/


    /*
        public function store(Request $request)
        {
            $schoolId = session('current_school_id'); // contexto da escola logada

            $request->validate([
                'nome_u'   => 'required|string|max:100',
                'cpf'      => 'required|string|max:11',
                'password' => 'required|string|min:6',
                'status'   => 'required|boolean',
                'roles'    => 'required|array'
            ]);

            // Verifica se já existe usuário com o mesmo CPF (em qualquer escola)
            $usuarioExistente = Usuario::where('cpf', $request->cpf)->first();

            if ($usuarioExistente) {
                // Se já existe, não cria de novo → redireciona com mensagem e opção de vincular
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('usuario_existente', $usuarioExistente->id);
            }

            // Caso não exista, cria novo usuário nesta escola
            $usuario = Usuario::create([
                'school_id'  => $schoolId,
                'cpf'        => $request->cpf,
                'senha_hash' => Hash::make($request->password),
                'nome_u'     => $request->nome_u,
                'status'     => $request->status,
            ]);

            // associa roles na tabela pivot (com contexto da escola)
            foreach ($request->roles as $roleId) {
                $usuario->roles()->attach($roleId, ['school_id' => $schoolId]);
            }

            // se for professor, cria também em syrios_professor
            $roleProfessorId = Role::where('role_name','professor')->first()->id;
            if (in_array($roleProfessorId, $request->roles)) {
                Professor::firstOrCreate([
                    'usuario_id' => $usuario->id,
                    'school_id'  => $schoolId
                ]);
            }

            return redirect()->route('escola.usuarios.index')->with('success','Usuário criado com sucesso!');
        }

        public function vincular(Request $request, $usuarioId)
        {
            $schoolId = session('current_school_id');

            if (!$schoolId) {
                return redirect()->route('escola.usuarios.index')
                    ->with('error', 'Nenhuma escola selecionada no contexto.');
            }

            $usuario = Usuario::findOrFail($usuarioId);

            $request->validate([
                'roles' => 'required|array'
            ]);

            foreach ($request->roles as $roleId) {
                // só vincula se ainda não tiver
                $jaTem = $usuario->roles()
                    ->where('role_id', $roleId)
                    ->wherePivot('school_id', $schoolId)
                    ->exists();

                if (!$jaTem) {
                    $usuario->roles()->attach($roleId, ['school_id' => $schoolId]);
                }

                // se professor → cria também no syrios_professor
                $roleProfessorId = Role::where('role_name','professor')->first()->id;
                if ($roleId == $roleProfessorId) {
                    Professor::firstOrCreate([
                        'usuario_id' => $usuario->id,
                        'school_id'  => $schoolId
                    ]);
                }
            }

            return redirect()->route('escola.usuarios.index')
                ->with('success', 'Usuário vinculado à escola com sucesso!');
        }
        */

    /*
    public function vincular($usuarioId)
    {
        $schoolId = session('current_school_id'); // contexto da escola logada
        if (!$schoolId) {
            return redirect()->route('escola.usuarios.index')
                ->with('error', 'Nenhuma escola selecionada no contexto.');
        }

        $usuario = Usuario::findOrFail($usuarioId);

        // Verifica se já tem a role professor nessa escola
        $roleProfessor = Role::where('role_name', 'professor')->firstOrFail();
        $jaVinculado = $usuario->roles()
            ->where('role_id', $roleProfessor->id)
            ->wherePivot('school_id', $schoolId)
            ->exists();

        if ($jaVinculado) {
            return redirect()->route('escola.usuarios.index')
                ->with('info', 'Usuário já vinculado como professor nesta escola.');
        }

        // Vincula o usuário como professor na escola atual
        $usuario->roles()->attach($roleProfessor->id, ['school_id' => $schoolId]);

        // Garante registro na tabela professor
        Professor::firstOrCreate([
            'usuario_id' => $usuario->id,
            'school_id'  => $schoolId
        ]);

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário vinculado à escola como professor com sucesso!');
    }*/

    /*
    public function store(Request $request)
    {
        //$escolaId = auth()->user()->school_id;
        $escolaId = session('current_school_id'); // escola logada

        $request->validate([
            'nome_u' => 'required|string|max:100',
            'cpf' => 'required|string|max:11',
            'password' => 'required|string|min:6',
            'status' => 'required|boolean',
            'roles' => 'required|array'
        ]);

        // Verifica se já existe usuário com o mesmo CPF
        $usuarioExistente = Usuario::where('cpf', $request->cpf)->first();

        if ($usuarioExistente) {
            // Já existe: redireciona com mensagem e opção de vincular
            return redirect()
                ->back()
                ->withInput()
                ->with('usuario_existente', $usuarioExistente->id);
        }

        // Caso não exista, cria normalmente
        $usuario = Usuario::create([
            'school_id' => $escolaId,
            'cpf' => $request->cpf,
            'senha_hash' => Hash::make($request->password),
            'nome_u' => $request->nome_u,
            'status' => $request->status,
        ]);

        // associa roles
        foreach ($request->roles as $roleId) {
            $usuario->roles()->attach($roleId, ['school_id' => $escolaId]);
        }

        // se tem role professor → cria em syrios_professor
        $roleProfessorId = Role::where('role_name','professor')->first()->id;
        if (in_array($roleProfessorId, $request->roles)) {
            Professor::firstOrCreate([
                'usuario_id' => $usuario->id,
                'school_id' => $escolaId
            ]);
        }

        return redirect()->route('escola.usuarios.index')->with('success','Usuário criado com sucesso!');
    }*/

    //antes
        // public function store(Request $request)
        // {
        //     //$escolaId = auth()->user()->school_id;
        //     $escolaId = session('current_school_id'); // escola logada

        //     $request->validate([
        //         'nome_u' => 'required|string|max:100',
        //         'cpf' => 'required|string|max:11',
        //         'password' => 'required|string|min:6',
        //         'status' => 'required|boolean',
        //         'roles' => 'required|array'
        //     ]);

        //     // Verifica se já existe usuário com o mesmo CPF
        //     $usuarioExistente = Usuario::where('cpf', $request->cpf)->first();

        //     if ($usuarioExistente) {
        //         // Já existe: redireciona com mensagem e opção de vincular
        //         return redirect()
        //             ->back()
        //             ->withInput()
        //             ->with('usuario_existente', $usuarioExistente->id);
        //     }

        //     // Caso não exista, cria normalmente
        //     $usuario = Usuario::create([
        //         'school_id' => $escolaId,
        //         'cpf' => $request->cpf,
        //         'senha_hash' => Hash::make($request->password),
        //         'nome_u' => $request->nome_u,
        //         'status' => $request->status,
        //     ]);

        //     // associa roles
        //     foreach ($request->roles as $roleId) {
        //         $usuario->roles()->attach($roleId, ['school_id' => $escolaId]);
        //     }

        //     // se tem role professor → cria em syrios_professor
        //     $roleProfessorId = Role::where('role_name','professor')->first()->id;
        //     if (in_array($roleProfessorId, $request->roles)) {
        //         Professor::firstOrCreate([
        //             'usuario_id' => $usuario->id,
        //             'school_id' => $escolaId
        //         ]);
        //     }

        //     return redirect()->route('escola.usuarios.index')->with('success','Usuário criado com sucesso!');
        // }

    //ia removeu função importante
        // public function store(Request $request)
        // {
        //     $schoolId = session('current_school_id');

        //     $request->validate([
        //         'nome_u'   => 'required|string|max:100',
        //         'cpf'      => 'required|string|max:11|unique:syrios_usuario,cpf',
        //         'password' => 'required|string|min:6',
        //         'status'   => 'required|boolean',
        //         'roles'    => 'required|array'
        //     ]);

        //     $usuario = Usuario::create([
        //         'school_id'  => $schoolId,
        //         'cpf'        => $request->cpf,
        //         'senha_hash' => Hash::make($request->password),
        //         'nome_u'     => $request->nome_u,
        //         'status'     => $request->status,
        //     ]);

        //     foreach ($request->roles as $roleId) {
        //         $usuario->roles()->attach($roleId, ['school_id' => $schoolId]);
        //     }

        //     // Se marcou como professor → cria no syrios_professor
        //     $roleProfessorId = Role::where('role_name','professor')->first()->id;
        //     if (in_array($roleProfessorId, $request->roles)) {
        //         Professor::firstOrCreate([
        //             'usuario_id' => $usuario->id,
        //             'school_id'  => $schoolId
        //         ]);
        //     }

        //     return redirect()->route('escola.usuarios.index')->with('success','Usuário criado com sucesso!');
        // }
    
    


    /*/vincular apenas com professor por enquanto
    public function vincular($usuarioId)
    {
        $schoolId = session('current_school_id');
        $usuario = Usuario::findOrFail($usuarioId);

        if (!$schoolId) {
            return back()->with('error', 'Nenhuma escola em contexto. Faça login novamente.');
        }

        // já está vinculado?
        if ($usuario->roles()->wherePivot('school_id', $schoolId)->exists()) {
            return redirect()->route('escola.usuarios.index')
                ->with('info', 'Usuário já está vinculado a esta escola.');
        }

        // Vincula como professor (ou outros roles selecionados futuramente)
        $usuario->roles()->attach(2, ['school_id' => $schoolId]);

        $usuario->roles()->attach($roleProfessor->id, [
            'school_id' => $schoolId
        ]);

        Professor::firstOrCreate([
            'usuario_id' => $usuario->id,
            'school_id' => $schoolId,
        ]);

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário vinculado com sucesso!');
    }*/


    /*
    🧾 Resumo das proteções aplicadas no Edit
        Regra   Situação    Resultado
        🔒 Usuário logado (self) Pode alterar senha apenas   
        🏫 Usuário nativo da escola  Pode alterar nome, senha, status    
        🔗 Usuário apenas vinculado  Somente leitura 
        🧍 Colega com role “escola”  Somente leitura 
        🚫 Usuário externo   Acesso negado   
        🔒 Usuário master/secretaria Somente leitura 
        ⚙️ Roles agrupadas por escola   Sempre exibidas (informativo)   
        🔗 Botão “Gerenciar roles”   Disponível em todos os casos informativos   
        🧩 Proteção total no controller e no blade   ✅ Coerência entre back e front
        */
    /*
    🧾 Resumo técnico
        Proteção    Onde é aplicada
        Bloqueio master/secretaria  edit() + update()
        Bloqueio entre gestores (“escola ↔ escola”) edit() + update()
        Acesso negado a usuários externos   edit()
        Edição restrita a nativos   update()
        Edição própria (somente senha)  update()
        Agrupamento de roles para exibição  edit()
        Redirecionamento seguro com mensagens amigáveis ✅
            */
    public function edit(Usuario $usuario)
    {
        $auth = auth()->user();
        $schoolId = session('current_school_id');

        if (!$schoolId) {
            return redirect()->route('escola.dashboard')->with('error', 'Nenhuma escola selecionada.');
        }

        $roles = $usuario->roles->pluck('role_name')->toArray();

        // 🧱 Identificações básicas
        $isSelf = $usuario->id === $auth->id;
        $isNativo = $usuario->school_id == $schoolId;
        $isVinculado = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->exists() && !$isNativo;

        $authTemRoleEscola = $auth->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        $alvoTemRoleEscola = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        $bloqueadoPorHierarquia = in_array('master', $roles) || in_array('secretaria', $roles);

        // 🚫 Proteções hierárquicas
        if (!$isNativo && !$isVinculado && !$isSelf) {
            return redirect()->route('escola.usuarios.index')
                ->with('error', 'Usuário não pertence nem está vinculado à sua escola.');
        }

        if ($bloqueadoPorHierarquia) {
            return view('escola.usuarios.view_only', compact('usuario'))
                ->with('warning', 'Usuário protegido por hierarquia superior.');
        }

        if ($authTemRoleEscola && $alvoTemRoleEscola && !$isSelf) {
            return view('escola.usuarios.view_only', compact('usuario'))
                ->with('warning', 'Gestor escolar não pode editar outro gestor da mesma escola.');
        }

        // 🔹 Roles agrupadas (para exibir)
        $rolesPorEscola = $usuario->roles()
            ->select('role_name', prefix('usuario_role') . '.school_id')
            ->get()
            ->groupBy('school_id');

        // ✅ Redireciona para view correta
        return view('escola.usuarios.edit', compact('usuario', 'rolesPorEscola'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $auth = auth()->user();
        $schoolId = session('current_school_id');

        if (!$schoolId) {
            return redirect()->route('escola.dashboard')->with('error', 'Nenhuma escola selecionada.');
        }

        $roles = $usuario->roles->pluck('role_name')->toArray();
        $isSelf = $usuario->id === $auth->id;
        $isNativo = $usuario->school_id == $schoolId;
        $authTemRoleEscola = $auth->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();
        $alvoTemRoleEscola = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();
        $bloqueadoPorHierarquia = in_array('master', $roles) || in_array('secretaria', $roles);

        // 🚫 Bloqueios gerais
        if ($bloqueadoPorHierarquia) {
            return back()->with('error', 'Usuário protegido — não pode ser alterado.');
        }

        if ($authTemRoleEscola && $alvoTemRoleEscola && !$isSelf) {
            return back()->with('error', 'Você não pode alterar outro gestor escolar.');
        }

        if (!$isSelf && !$isNativo) {
            return back()->with('error', 'Você não tem permissão para alterar este usuário.');
        }

        // ✅ Validação básica
        $validated = $request->validate([
            'nome_u' => 'nullable|string|max:100',
            'senha'  => 'nullable|string|min:6',
            'status' => 'nullable|boolean',
        ]);

        // 🧠 1️⃣ Caso o próprio usuário logado
        if ($isSelf) {
            if (!empty($validated['senha'])) {
                $usuario->update(['senha_hash' => bcrypt($validated['senha'])]);
                return back()->with('success', 'Senha alterada com sucesso!');
            }
            return back()->with('info', 'Nada foi alterado.');
        }

        // 🧠 2️⃣ Caso usuário nativo (criado pela escola)
        if ($isNativo) {
            $dadosAtualizados = [
                'nome_u' => $validated['nome_u'] ?? $usuario->nome_u,
                'status' => $validated['status'] ?? $usuario->status,
            ];

            if (!empty($validated['senha'])) {
                $dadosAtualizados['senha_hash'] = bcrypt($validated['senha']);
            }

            $usuario->update($dadosAtualizados);
            return redirect()->route('escola.usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
        }

        // 🔒 3️⃣ Caso seja vinculado (de outra escola)
        return back()->with('warning', 'Usuário vinculado — apenas o proprietário pode alterar seus dados.');
    }


    /*
    🧠 Resumo lógico
        Regra   Efeito
        🔒 Usuário master/secretaria intocável   Impede qualquer alteração de roles
        🧱 Somente roles da escola atual são modificadas Preserva vínculos com outras escolas
        👥 Gestor não edita outro gestor Segurança hierárquica local
        🙋 Gestor pode editar suas próprias roles (exceto remover sua role escola)   Autonomia controlada
        📋 Apenas roles permitidas (professor, aluno, pais...)   Coerência com contexto escolar
        */
    public function editRoles(Usuario $usuario)
    {
        $auth = auth()->user();
        $schoolId = session('current_school_id');
        $escolaAtual = Escola::find($schoolId);

        if (!$escolaAtual) {
            return redirect()->route('escola.dashboard')->with('error', 'Nenhuma escola selecionada.');
        }

        // 🧱 1️⃣ Protege contra acesso fora do escopo da escola
        $vinculadoAqui = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->exists();

        if ($usuario->school_id !== $schoolId && !$vinculadoAqui) {
            return redirect()->route('escola.usuarios.index')
                ->with('error', 'Usuário não pertence nem está vinculado a esta escola.');
        }

        // 🧱 2️⃣ Bloqueio entre gestores da mesma escola
        $authTemRoleEscola = $auth->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        $alvoTemRoleEscola = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        if ($authTemRoleEscola && $alvoTemRoleEscola && $auth->id !== $usuario->id) {
            return redirect()->route('escola.usuarios.index')
                ->with('error', 'Você não pode alterar as roles de outro gestor desta escola.');
        }

        // 🧱 3️⃣ Carrega apenas roles permitidas no contexto da escola
        $roles = Role::whereNotIn('role_name', ['master', 'secretaria'])->get();

        return view('escola.usuarios.roles_edit', compact('usuario', 'roles', 'escolaAtual'));
    }


    public function updateRoles(Request $request, Usuario $usuario)
    {
        $auth = auth()->user();
        $schoolId = session('current_school_id');
        $escolaAtual = Escola::find($schoolId);

        if (!$escolaAtual) {
            return redirect()->route('escola.dashboard')->with('error', 'Nenhuma escola selecionada.');
        }

        $request->validate([
            'roles' => 'nullable|array'
        ]);

        $rolesSelecionadas = $request->roles ?? [];

        // 🧱 1️⃣ Protege hierarquia superior
        $rolesSuperiores = $usuario->roles()
            ->whereIn('role_name', ['master', 'secretaria'])
            ->exists();

        if ($rolesSuperiores) {
            return back()->with('error', 'Usuário com role superior não pode ter roles alteradas pela escola.');
        }

        // 🧱 2️⃣ Protege gestores (role escola) de outros gestores
        $authTemRoleEscola = $auth->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        $alvoTemRoleEscola = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        if ($authTemRoleEscola && $alvoTemRoleEscola && $auth->id !== $usuario->id) {
            return back()->with('error', 'Você não pode alterar as roles de outro gestor escolar.');
        }

        // 🧱 3️⃣ Impede remover a própria role "escola"
        $roleEscolaId = Role::where('role_name', 'escola')->value('id');
        if ($auth->id === $usuario->id && !in_array($roleEscolaId, $rolesSelecionadas)) {
            return back()->with('error', 'Você não pode remover sua própria role de gestor da escola.');
        }

        // 🧱 4️⃣ Remove apenas roles dessa escola
        DB::table(prefix('usuario_role'))
            ->where('usuario_id', $usuario->id)
            ->where('school_id', $schoolId)
            ->delete();

        // 🧱 5️⃣ Reinsere roles selecionadas (somente válidas)
        $rolesPermitidas = Role::whereNotIn('role_name', ['master', 'secretaria'])
            ->pluck('id')
            ->toArray();

        foreach ($rolesSelecionadas as $roleId) {
            if (in_array($roleId, $rolesPermitidas)) {
                DB::table(prefix('usuario_role'))->insertOrIgnore([
                    'usuario_id' => $usuario->id,
                    'role_id'    => $roleId,
                    'school_id'  => $schoolId
                ]);
            }
        }

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Roles do usuário atualizadas com sucesso.');
    }






    /*public function edit(Usuario $usuario)
    {
        $this->authorizeEscola($usuario);

        $roles = Role::whereNotIn('role_name', ['master','secretaria','escola'])->get();
        $usuarioRoles = $usuario->roles()->pluck('role_id')->toArray();

        return view('escola.usuarios.edit', compact('usuario','roles','usuarioRoles'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $this->authorizeEscola($usuario);

        $request->validate([
            'nome_u' => 'required|string|max:100',
            'cpf' => 'required|string|max:11|unique:syrios_usuario,cpf,' . $usuario->id,
            'status' => 'required|boolean',
            'roles' => 'required|array'
        ]);

        // atualiza usuário
        $usuario->update([
            'cpf' => $request->cpf,
            'nome_u' => $request->nome_u,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $usuario->update([
                'senha_hash' => Hash::make($request->password)
            ]);
        }

        // sincroniza roles
        $escolaId = auth()->user()->school_id;
        $usuario->roles()->sync([]);
        foreach ($request->roles as $roleId) {
            $usuario->roles()->attach($roleId, ['school_id' => $escolaId]);
        }

        // sincroniza professor
        $roleProfessorId = Role::where('role_name','professor')->first()->id;
        $temProfessor = Professor::where('usuario_id', $usuario->id)->exists();
        $querProfessor = in_array($roleProfessorId, $request->roles);

        if ($temProfessor && !$querProfessor) {
            Professor::where('usuario_id',$usuario->id)->delete();
        } elseif (!$temProfessor && $querProfessor) {
            Professor::create(['usuario_id'=>$usuario->id,'school_id'=>$escolaId]);
        } elseif ($temProfessor && $querProfessor) {
            Professor::where('usuario_id',$usuario->id)->update(['school_id'=>$escolaId]);
        }

        return redirect()->route('escola.usuarios.index')->with('success','Usuário atualizado com sucesso!');
    }*/


    /*
    🧾 Resumo de todas as proteções aplicadas
        Regra /  Proteção aplicada
        ❌ Não excluir master/super master /  ✅
        ❌ Não excluir secretaria  /  ✅
        ❌ Não excluir a si mesmo  /  ✅
        ❌ Não excluir outro “escola” se for “escola”  /  ✅
        ❌ Não excluir com dependências (professor, aluno, turma, ocorrência)  /  ✅
        🔗 Se for apenas vinculado → remover vínculo, não excluir  /  ✅
        👨‍🏫 Se for professor → remover da tabela syrios_professor /  ✅
        ✅ Excluir totalmente só se for dono (school_id igual à atual) /  ✅
        💥 Tratar exceções e mensagens amigáveis / ✅
        */
    public function destroy(Usuario $usuario)
    {
        $schoolId = session('current_school_id');
        $auth = auth()->user();

        // 🔒 1️⃣ Proteções básicas
        if ($usuario->is_super_master || $usuario->roles->pluck('role_name')->contains('master')) {
            return back()->with('error', '🚫 Não é permitido excluir usuários master ou super master.');
        }

        if ($usuario->roles->pluck('role_name')->contains('secretaria')) {
            return back()->with('error', '🚫 Usuários com papel de secretaria não podem ser excluídos por escolas.');
        }

        // 🚫 2️⃣ O usuário não pode se excluir
        if ($usuario->id === $auth->id) {
            return back()->with('error', '🚫 Você não pode excluir a si mesmo.');
        }

        // 🚫 3️⃣ Usuário com role "escola" não pode excluir outro "escola"
        $authTemRoleEscola = $auth->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        $alvoTemRoleEscola = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        if ($authTemRoleEscola && $alvoTemRoleEscola) {
            return back()->with('error', '🚫 Usuário com papel de gestão escolar não pode excluir outro gestor da mesma escola.');
        }

        // 🔍 4️⃣ Verifica se pertence a esta escola
        $isNativo = $usuario->school_id == $schoolId;

        try {
            if ($isNativo) {
                // 💣 Excluir totalmente o usuário apenas se não houver dependências
                $possuiDependencias = DB::table(prefix('professor'))->where('usuario_id', $usuario->id)->exists()
                    || DB::table(prefix('aluno'))->where('usuario_id', $usuario->id)->exists()
                    || DB::table(prefix('ocorrencia'))->where('usuario_id', $usuario->id)->exists()
                    || DB::table(prefix('diretor_turma'))->where('usuario_id', $usuario->id)->exists();

                if ($possuiDependencias) {
                    return back()->with('error', '⚠️ Não é possível excluir este usuário, pois ele possui registros vinculados.');
                }

                // Remove vínculos da tabela pivot
                $usuario->roles()->detach();

                // Remove também vínculo em syrios_professor (se existir)
                DB::table(prefix('professor'))
                    ->where('usuario_id', $usuario->id)
                    ->where('school_id', $schoolId)
                    ->delete();

                // Agora exclui o usuário
                $usuario->delete();

                return back()->with('success', '✅ Usuário excluído com sucesso.');
            }

            // 🧩 5️⃣ Se for apenas vinculado (pivot)
            $pivotRoles = $usuario->roles()
                ->wherePivot('school_id', $schoolId)
                ->pluck('role_id')
                ->toArray();

            if (empty($pivotRoles)) {
                return back()->with('warning', '⚠️ Este usuário não possui vínculo com a escola atual.');
            }

            // Verifica se pode remover (sem violar dependências)
            $possuiProfessor = DB::table(prefix('professor'))
                ->where('usuario_id', $usuario->id)
                ->where('school_id', $schoolId)
                ->exists();

            if ($possuiProfessor) {
                DB::table(prefix('professor'))
                    ->where('usuario_id', $usuario->id)
                    ->where('school_id', $schoolId)
                    ->delete();
            }

            // Remove vínculos apenas desta escola
            DB::table(prefix('usuario_role'))
                ->where('usuario_id', $usuario->id)
                ->where('school_id', $schoolId)
                ->delete();

            return back()->with('success', '✅ Vínculo do usuário com a escola removido com sucesso.');

        } catch (\Throwable $e) {
            return back()->with('error', '❌ Erro ao excluir usuário: ' . $e->getMessage());
        }
    }



    //cuidado: delete absoluto de todas as escolas
        // public function destroy(Usuario $usuario)
        // {
        //     $this->authorizeEscola($usuario);

        //     // Remove vínculos na pivot roles
        //     $usuario->roles()->detach();

        //     // Se ele for professor, remove vínculo primeiro
        //     \App\Models\Professor::where('usuario_id', $usuario->id)->delete();

        //     $usuario->delete();
        //     return redirect()->route('escola.usuarios.index')->with('success','Usuário excluído com sucesso!');
        // }

    //aqui é mais cuidadoso
        // public function destroy(Usuario $usuario)
        // {
        //     $currentSchoolId = session('current_school_id');

        //     // regra:remove apenas o vínculo na escola atual logada
        //     $usuario->roles()->wherePivot('school_id', $currentSchoolId)->detach();

        //     // regra:se ele era professor nessa escola, remove também
        //     Professor::where('usuario_id', $usuario->id)
        //              ->where('school_id', $currentSchoolId)
        //              ->delete();

        //     // regra:verifica se ainda tem algum vínculo em outras escolas antes de apaga-lo
        //     if ($usuario->roles()->count() === 0) {
        //         $usuario->delete();
        //     }

        //     return redirect()->route('escola.usuarios.index')
        //         ->with('success', 'Usuário desvinculado da escola com sucesso!');
        // }
        //*/

    private function authorizeEscola($usuario)
    {
        if ($usuario->school_id !== auth()->user()->school_id) {
            abort(403, 'Acesso negado.');
        }
    }
}






/*
namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Role;

class UsuarioController extends Controller
{
   
    public function index()
    {
        $escola = auth()->user()->escola;
        $usuarioLogadoId = auth()->id();

        if (!$escola) {
            return redirect()->route('escola.dashboard')->with('error', 'Nenhuma escola vinculada.');
        }

        // lista somente os usuários criados por esta escola
        $usuarios = Usuario::where('school_id', $escola->id)
            ->where('id', '<>', $usuarioLogadoId) // exclui o logado
            ->with('roles')
            ->get();

        return view('escola.usuarios.index', compact('usuarios', 'escola'));
    }

    
    public function create()
    {
        $escola = auth()->user()->escola;
        $roles = Role::whereNotIn('role_name', ['master','secretaria'])->get();

        return view('escola.usuarios.create', compact('escola','roles'));
    }

    
    public function store(Request $request)
    {
        $escola = auth()->user()->escola;

        $request->validate([
            'nome_u' => 'required|string|max:255',
            'cpf' => 'required|string|max:20|unique:syrios_usuario,cpf',
            'senha' => 'required|string|min:6',
            'roles'  => 'required|array',
        ]);

        $usuario = Usuario::create([
            'nome_u'     => $request->nome_u,
            'cpf'        => $request->cpf,
            'senha_hash' => bcrypt($request->senha),
            'status'     => 1,
            'school_id'  => $escola->id, // dono é a escola logada
        ]);

        // monta array de roles com school_id fixo
        $rolesSync = [];
        foreach ($request->roles as $role_id) {
            $rolesSync[$role_id] = ['school_id' => $escola->id];
        }

        // sincroniza pivot
        $usuario->roles()->sync($rolesSync);
        //$usuario->roles()->sync($request->roles);

        // depois de salvar usuario
        if (in_array($roleProfessorId, $request->roles ?? [])) {
            Professor::firstOrCreate([
                'usuario_id' => $usuario->id,
                'school_id'  => $usuario->school_id, // mesma escola do usuário
            ]);
        }

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    
    public function edit($id)
    {
        $escola = auth()->user()->escola;
        $usuario = Usuario::where('school_id', $escola->id)->findOrFail($id);
        $roles = Role::whereNotIn('role_name', ['master','secretaria'])->get();

        return view('escola.usuarios.edit', compact('usuario','escola','roles'));
    }

    
    public function update(Request $request, $id)
    {
        $escola = auth()->user()->escola;

        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nome_u' => 'required|string|max:100',
            'cpf' => 'required|string|max:11',
            'status' => 'required|boolean',
            'senha' => 'nullable|string|min:6',
            'roles' => 'array'
        ]);

        // Atualiza dados básicos
        $updateData = [
            'nome_u' => $validated['nome_u'],
            'cpf' => $validated['cpf'],
            'status' => $validated['status'],
        ];

        if ($request->filled('senha')) {
            $updateData['senha_hash'] = Hash::make($request->senha);
        }

        $usuario->update($updateData);

        // Atualiza roles
        $rolesSync = [];
        if ($request->has('roles')) {
            foreach ($request->roles as $role_id) {
                $rolesSync[$role_id] = ['school_id' => $escola->id];
            }
        }
        $usuario->roles()->sync($rolesSync);

        //atualiza também a tabela pofessor
        $temProfessor = Professor::where('usuario_id', $usuario->id)->exists();
        $querProfessor = in_array($roleProfessorId, $request->roles ?? []);

        if ($temProfessor && !$querProfessor) {
            Professor::where('usuario_id', $usuario->id)->delete();
        } elseif (!$temProfessor && $querProfessor) {
            Professor::create([
                'usuario_id' => $usuario->id,
                'school_id'  => $usuario->school_id,
            ]);
        } elseif ($temProfessor && $querProfessor) {
            // garante que está com a escola correta
            Professor::where('usuario_id', $usuario->id)->update([
                'school_id' => $usuario->school_id
            ]);
        }

        return redirect()->route('escola.usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    
    public function update(Request $request, $id)
    {
        $escola = auth()->user()->escola;
        $usuario = Usuario::where('school_id', $escola->id)->findOrFail($id);

        $request->validate([
            'nome_u' => 'required|string|max:255',
            'cpf'    => 'required|string|max:20|unique:syrios_usuario,cpf,'.$usuario->id,
            'status' => 'required|boolean',
            'roles'  => 'required|array',
        ]);

        $usuario->update([
            'nome_u' => $request->nome_u,
            'cpf'    => $request->cpf,
            'status' => $request->status,
        ]);

        if ($request->filled('senha')) {
            $usuario->update(['senha_hash' => bcrypt($request->senha)]);
        }

        $rolesSync = [];
        if ($request->has('roles')) {
            foreach ($request->roles as $role_id) {
                $rolesSync[$role_id] = ['school_id' => $escola->id];
            }
        }
        $usuario->roles()->sync($rolesSync);
        //$usuario->roles()->sync($request->roles);

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

   
    public function destroy($id)
    {
        $escola = auth()->user()->escola;
        $usuario = Usuario::where('school_id', $escola->id)->findOrFail($id);

        $usuario->roles()->detach();
        $usuario->delete();

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
*/