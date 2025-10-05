<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Role;
use App\Models\Professor;
use Illuminate\Support\Facades\Hash;

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

    public function create()
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


    public function edit(Usuario $usuario)
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
    }

    //cuidado: delete absoluto de todas as escolas
    public function destroy(Usuario $usuario)
    {
        $this->authorizeEscola($usuario);

        // Remove vínculos na pivot roles
        $usuario->roles()->detach();

        // Se ele for professor, remove vínculo primeiro
        \App\Models\Professor::where('usuario_id', $usuario->id)->delete();

        $usuario->delete();
        return redirect()->route('escola.usuarios.index')->with('success','Usuário excluído com sucesso!');
    }

    /*aqui é mais cuidadoso
    public function destroy(Usuario $usuario)
    {
        $currentSchoolId = session('current_school_id');

        // remove apenas o vínculo na escola atual
        $usuario->roles()->wherePivot('school_id', $currentSchoolId)->detach();

        // se ele era professor nessa escola, remove também
        Professor::where('usuario_id', $usuario->id)
                 ->where('school_id', $currentSchoolId)
                 ->delete();

        // verifica se ainda tem algum vínculo em outras escolas
        if ($usuario->roles()->count() === 0) {
            $usuario->delete();
        }

        return redirect()->route('escola.usuarios.index')
            ->with('success', 'Usuário desvinculado da escola com sucesso!');
    }
    */

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