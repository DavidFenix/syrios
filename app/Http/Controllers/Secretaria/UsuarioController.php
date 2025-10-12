<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\{Usuario, Escola, Role};
use Illuminate\Support\Facades\DB;


class UsuarioController extends Controller
{
    
    public function index()
    {
        $currentSchoolId = session('current_school_id');

        if (!$currentSchoolId) {
            return redirect()->route('home')->with('error', 'Nenhuma escola selecionada no momento.');
        }

        $secretaria = Escola::find($currentSchoolId);

        if (!$secretaria) {
            return redirect()->route('home')->with('error', 'Escola atual não encontrada.');
        }

        // 🧩 1. Identifica todas as escolas da secretaria (ela mesma + filhas)
        $idsEscolas = collect([$secretaria->id])
            ->merge($secretaria->filhas()->pluck('id'))
            ->unique();

        // 🧩 2. Busca usuários:
        // - cujo school_id pertence à secretaria ou filhas (usuário "nativo" da escola)
        // - OU que estejam vinculados via pivot (usuario_role.school_id)
        $usuarios = Usuario::whereIn('school_id', $idsEscolas)
            ->orWhereHas('roles', function ($q) use ($idsEscolas) {
                $q->whereIn(prefix('usuario_role') . '.school_id', $idsEscolas);
            })
            ->with(['escola', 'roles'])
            ->get()
            //->unique('id') // evita duplicatas se o usuário aparecer nas duas condições
            ->values();

        return view('secretaria.usuarios.index', compact('usuarios', 'secretaria'));
    }


    /*public function index()
    {
        // Obtém o ID da escola atual da sessão
        $currentSchoolId = session('current_school_id');

        // Verifica se há uma escola selecionada
        if (!$currentSchoolId) {
            return redirect()->route('home')->with('error', 'Nenhuma escola selecionada no momento.');
        }

        // Busca a escola (secretaria) correspondente
        $secretaria = Escola::find($currentSchoolId);

        // Garante que seja válida
        if (!$secretaria) {
            return redirect()->route('home')->with('error', 'Escola atual não encontrada.');
        }

        // Pega todos os usuários das escolas filhas da secretaria atual
        $usuarios = Usuario::whereIn('school_id', $secretaria->filhas()->pluck('id'))
            ->with(['escola', 'roles'])
            ->get();

        return view('secretaria.usuarios.index', compact('usuarios', 'secretaria'));
    }*/

        /**
     * Exibe o formulário de criação de usuário da secretaria.
     */
    public function create()
    {
        $auth = auth()->user();

        // 🔒 Lista apenas as escolas da própria secretaria + filhas
        $escolas = Escola::where(function ($q) use ($auth) {
            $q->where('id', $auth->school_id)
              ->orWhere('secretaria_id', $auth->school_id);
        })->get();

        $roles = Role::whereNotIn('role_name', ['master', 'secretaria'])->get();

        return view('secretaria.usuarios.create', compact('escolas', 'roles'));

    }

    /**
     * Cria novo usuário ou oferece vinculação a existente.
     */
    public function store(Request $request)
    {
        $auth = auth()->user();

        $request->validate([
            'nome_u'    => 'required|string|max:100',
            'cpf'       => 'required|string|max:20',
            'school_id' => 'required|integer',
        ]);

        // 🔒 Garante que a escola pertence à secretaria **e não é a própria secretaria**
        $escolaAutorizada = Escola::where('id', $request->school_id)
            ->where('secretaria_id', $auth->school_id)
            ->exists();

        if (!$escolaAutorizada) {
            return back()
                ->withInput()
                ->with('error', '🚫 Você só pode criar usuários em escolas filhas da sua secretaria (não na própria secretaria).');
        }


        // // 🔒 Garante que a escola pertence à secretaria
        // $escolaAutorizada = Escola::where('id', $request->school_id)
        //     ->where(function ($q) use ($auth) {
        //         $q->where('id', $auth->school_id)
        //           ->orWhere('secretaria_id', $auth->school_id);
        //     })
        //     ->exists();

        // if (!$escolaAutorizada) {
        //     return back()
        //         ->withInput()
        //         ->with('error', 'Você só pode criar usuários em escolas da sua secretaria ou filhas.');
        // }

        // 🔎 Verifica CPF existente
        $usuarioExistente = Usuario::where('cpf', $request->cpf)->first();

        if ($usuarioExistente) {
            // 🚫 Super Master
            if ($usuarioExistente->is_super_master) {
                return back()->with('error', 'Este CPF pertence ao Super Master e não pode ser vinculado.')->withInput();
            }

            // 🚫 Master
            if ($usuarioExistente->roles->pluck('role_name')->contains('master')) {
                return back()->with('error', 'Este CPF pertence a um Master. Somente o Super Master pode vinculá-lo.')->withInput();
            }

            // ✅ CPF existente, mas permitido
            return back()
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

        // 🔗 Vincula roles (sempre dentro da hierarquia da secretaria)
        if ($request->filled('roles')) {
            foreach ($request->roles as $roleId) {
                $usuario->roles()->attach($roleId, ['school_id' => $request->school_id]);
            }
        }

        return redirect()
            ->route('secretaria.usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function vincular(Request $request, $usuarioId)
    {
        $usuario = Usuario::findOrFail($usuarioId);
        $auth = auth()->user();
        $currentSchoolId = session('current_school_id');

        $request->validate([
            'school_id' => 'required|integer|exists:' . prefix('escola') . ',id',
            'roles'     => 'array|required|min:1'
        ]);

        // 🚫 Secretaria não pode vincular usuários na própria secretaria
        $currentSchoolId = session('current_school_id');
        if ($request->school_id == $currentSchoolId) {
            return back()->with('error', '🚫 Não é permitido adicionar usuários à própria secretaria.');
        }


        $novaEscola = Escola::find($request->school_id);

        // 🧱 1️⃣ Impede duplicação exata (mesmo user, escola, role)
        $duplicadas = [];
        foreach ($request->roles as $roleId) {
            $jaExiste = DB::table(prefix('usuario_role'))
                ->where('usuario_id', $usuario->id)
                ->where('role_id', $roleId)
                ->where('school_id', $novaEscola->id)
                ->exists();

            if ($jaExiste) {
                $duplicadas[] = $roleId;
            }
        }

        if (!empty($duplicadas)) {
            $nomes = Role::whereIn('id', $duplicadas)->pluck('role_name')->implode(', ');
            return back()->with('warning', "⚠️ O usuário já possui as roles: {$nomes} nessa escola.");
        }

        // 🧱 2️⃣ Impede o usuário de se vincular à mesma secretaria onde está logado
        if ($novaEscola->id == $currentSchoolId) {
            return back()->with('warning', '⚠️ O usuário já pertence à secretaria atual.');
        }

        // 🧱 3️⃣ Impede criar/vincular usuários diretamente na própria secretaria logada
        $currentSchoolId = session('current_school_id');
        $novaEscola = Escola::find($request->school_id);

        // Secretaria logada só pode atuar sobre escolas filhas, nunca sobre ela mesma
        if ($novaEscola && $novaEscola->id == $currentSchoolId) {
            return back()->with('error', '🚫 Você não pode criar ou vincular usuários diretamente nesta Secretaria. Use o painel Master para isso.');
        }

        // 🧩 Permite que um usuário (até mesmo com role secretaria) tenha outras roles em escolas filhas
        // Exemplo válido: usuario com role secretaria → também tem role professor em uma escola filha


        // // 🧱 3️⃣ Impede que uma Secretaria seja vinculada como Escola
        // $rolesSelecionadas = Role::whereIn('id', $request->roles)->pluck('role_name')->toArray();
        // $rolesAtuaisUsuario = $usuario->roles->pluck('role_name')->toArray();

        // if (in_array('secretaria', $rolesAtuaisUsuario) && in_array('escola', $rolesSelecionadas)) {
        //     return back()->with('error', '🚫 Uma Secretaria não pode ser vinculada como Escola.');
        // }

        // 🧱 4️⃣ Protege Super Master e Master
        if ($usuario->is_super_master && !$auth->is_super_master) {
            return back()->with('error', '🚫 Não é permitido vincular o Super Master a outras escolas.');
        }

        if ($usuario->roles->pluck('role_name')->contains('master') && !$auth->is_super_master) {
            if ($auth->cpf !== $usuario->cpf) {
                return back()->with('error', '🚫 Apenas o próprio Master ou o Super Master podem vincular um Master.');
            }
        }

        // ✅ 5️⃣ Tudo certo — cria vínculos
        foreach ($request->roles as $roleId) {
            DB::table(prefix('usuario_role'))->insert([
                'usuario_id' => $usuario->id,
                'role_id'    => $roleId,
                'school_id'  => $novaEscola->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ✅ 6️⃣ Atualiza data de atualização do usuário
        $usuario->touch();

        return redirect()
            ->route('secretaria.usuarios.index')
            ->with('success', "✅ Usuário '{$usuario->nome_u}' vinculado à escola '{$novaEscola->nome_e}' com sucesso!");
    }


    /*
    public function vincular(Request $request, $usuarioId)
    {
        $usuario = Usuario::findOrFail($usuarioId);
        $auth = auth()->user();

        $request->validate([
            'school_id' => 'required|integer|exists:' . prefix('escola') . ',id',
            'roles'     => 'array|required|min:1'
        ]);

        $novaEscola = Escola::find($request->school_id);

        // 🧱 1️⃣ Impede duplicação exata (mesmo user, escola, role)
        foreach ($request->roles as $roleId) {
            $jaExiste = DB::table(prefix('usuario_role'))
                ->where('usuario_id', $usuario->id)
                ->where('role_id', $roleId)
                ->where('school_id', $novaEscola->id)
                ->exists();

            if ($jaExiste) {
                return back()->with('warning', "⚠️ O usuário já está vinculado a esta escola com a role selecionada.");
            }
        }

        // 🧱 2️⃣ Impede o usuário de se vincular à mesma secretaria onde está logado
        $currentSchoolId = session('current_school_id');
        if ($novaEscola->id == $currentSchoolId) {
            return back()->with('warning', '⚠️ O usuário já pertence à escola/secretaria atual.');
        }

        // 🧱 3️⃣ Impede que uma secretaria seja vinculada como escola
        $rolesSelecionadas = Role::whereIn('id', $request->roles)->pluck('role_name')->toArray();

        if (in_array('secretaria', $usuario->roles->pluck('role_name')->toArray()) && in_array('escola', $rolesSelecionadas)) {
            return back()->with('error', '🚫 Uma Secretaria não pode ser vinculada como Escola.');
        }

        // 🧱 4️⃣ Protege super master e master
        if ($usuario->is_super_master && !$auth->is_super_master) {
            return back()->with('error', '🚫 Não é permitido vincular o Super Master a outras escolas.');
        }

        if ($usuario->roles->pluck('role_name')->contains('master') && !$auth->is_super_master) {
            if ($auth->cpf !== $usuario->cpf) {
                return back()->with('error', '🚫 Apenas o próprio Master ou o Super Master podem vincular um Master.');
            }
        }

        // ✅ 5️⃣ Tudo certo — cria vínculos
        foreach ($request->roles as $roleId) {
            DB::table(prefix('usuario_role'))->insert([
                'usuario_id' => $usuario->id,
                'role_id'    => $roleId,
                'school_id'  => $novaEscola->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()
            ->route('secretaria.usuarios.index')
            ->with('success', "✅ Usuário '{$usuario->nome_u}' vinculado à escola {$novaEscola->nome_e} com sucesso!");
    }

    public function vincular(Request $request, $usuarioId)
    {
        $auth = auth()->user();
        $usuario = Usuario::findOrFail($usuarioId);

        $request->validate([
            'school_id' => 'required|integer',
            'roles'     => 'array|required',
        ]);

        // 🔒 Valida se escola pertence à secretaria
        $escolaAutorizada = Escola::where('id', $request->school_id)
            ->where(function ($q) use ($auth) {
                $q->where('id', $auth->school_id)
                  ->orWhere('secretaria_id', $auth->school_id);
            })
            ->exists();

        if (!$escolaAutorizada) {
            return back()->with('error', 'A escola selecionada não pertence à sua secretaria.');
        }

        // 🚫 Proteções adicionais
        if ($usuario->is_super_master || $usuario->roles->pluck('role_name')->contains('master')) {
            return back()->with('error', 'Não é permitido vincular Masters ou Super Masters a outras escolas.');
        }

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
            ->route('secretaria.usuarios.index')
            ->with('success', 'Usuário existente vinculado com sucesso!');
    }*/

    /*
    public function index()
    {
        $secretaria = auth()->user()->escola;

        if (!$secretaria) {
            return redirect()->route('home')->with('error', 'Nenhuma secretaria vinculada.');
        }

        // pega todos os usuários das escolas filhas da secretaria logada
        $usuarios = Usuario::whereIn('school_id', $secretaria->filhas()->pluck('id'))
            ->with(['escola','roles'])
            ->get();

        return view('secretaria.usuarios.index', compact('usuarios','secretaria'));
    }

    public function create()
    {
        $secretaria = auth()->user()->escola;
        
        if (!$secretaria) {
            return redirect()->route('home')->with('error', 'Nenhuma secretaria vinculada.');
        }

        // secretária e suas filhas
        $filhas = $secretaria->filhas()->get();
        $escolas = collect([$secretaria])->merge($filhas);

        //$escolas = $secretaria->filhas;
        //$roles = Role::where('role_name', '!=', 'master')->get();
        
        // filtrar roles: exclui master e secretaria
        $roles = Role::whereNotIn('role_name', ['master', 'secretaria'])->get();

        return view('secretaria.usuarios.create', compact('escolas','roles'));
    }

    public function store(Request $request)
    {
        //dd($request->all()); // <- debug, vai mostrar os dados enviados

        $secretaria = auth()->user()->escola;

        $filhasIds = $secretaria->filhas()->pluck('id')->toArray();
        $permitidos = array_merge([$secretaria->id], $filhasIds);

        if (! in_array($request->school_id, $permitidos)) {
            return back()->with('error', 'Escola inválida para esta secretaria.');
        }

        // 🔒 Validação
        $request->validate([
            'nome_u'   => 'required|string|max:100',
            'cpf'      => 'required|string|max:11',
            'senha'    => 'required|string|min:6',
            'status'   => 'required|boolean',
            'school_id'=> 'required|exists:syrios_escola,id',
            'roles'    => 'array',
            'roles.*'  => 'exists:syrios_role,id',
        ]);

        // 🔒 Garante que a escola escolhida pertence à secretaria logada
        if (!$secretaria->filhas->pluck('id')->contains($request->school_id)) {
            return back()->withErrors('Escola inválida para esta secretaria.');
        }

        // 🔨 Cria o usuário
        $usuario = Usuario::create([
            'nome_u'    => $request->nome_u,
            'cpf'       => $request->cpf,
            'senha_hash'=> Hash::make($request->senha),
            'status'    => $request->status,
            'school_id' => $request->school_id,
        ]);

        // 🔨 Vincula roles (com school_id)
        $rolesSync = [];
        foreach ($request->roles ?? [] as $role_id) {
            $rolesSync[$role_id] = ['school_id' => $request->school_id];
        }
        $usuario->roles()->sync($rolesSync);

        return redirect()->route('secretaria.usuarios.index')
            ->with('success', 'Usuário criado com sucesso.');
    }*/

    public function update(Request $request, Usuario $usuario)
    {
        $secretaria = auth()->user()->escola;

        $filhasIds = $secretaria->filhas()->pluck('id')->toArray();
        $permitidos = array_merge([$secretaria->id], $filhasIds);

        if (! in_array($request->school_id, $permitidos)) {
            return back()->with('error', 'Escola inválida para esta secretaria.');
        }

        // 🔒 Validação
        $request->validate([
            'nome_u'   => 'required|string|max:100',
            'cpf'      => 'required|string|max:11',
            'status'   => 'required|boolean',
            'school_id'=> 'required|exists:syrios_escola,id',
            'senha'    => 'nullable|string|min:6',
            'roles'    => 'array',
            'roles.*'  => 'exists:syrios_role,id',
        ]);

        // 🔒 Garante que a escola escolhida pertence à secretaria logada
        if (!$secretaria->filhas->pluck('id')->contains($request->school_id)) {
            return back()->withErrors('Escola inválida para esta secretaria.');
        }

        // 🔨 Atualiza usuário
        $usuario->update([
            'nome_u'    => $request->nome_u,
            'cpf'       => $request->cpf,
            'status'    => $request->status,
            'school_id' => $request->school_id,
        ]);

        // Atualiza senha (se enviada)
        if ($request->filled('senha')) {
            $usuario->update(['senha_hash' => Hash::make($request->senha)]);
        }

        // 🔨 Atualiza roles (com school_id)
        $rolesSync = [];
        foreach ($request->roles ?? [] as $role_id) {
            $rolesSync[$role_id] = ['school_id' => $request->school_id];
        }
        $usuario->roles()->sync($rolesSync);

        return redirect()->route('secretaria.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /*
    public function store(Request $request)
    {
        $request->validate([
            'nome_u' => 'required',
            'cpf' => 'required|unique:syrios_usuario,cpf',
            'senha' => 'required|min:6',
            'school_id' => 'required|exists:syrios_escola,id',
        ]);

        $usuario = Usuario::create([
            'nome_u' => $request->nome_u,
            'cpf' => $request->cpf,
            'senha_hash' => Hash::make($request->senha),
            'status' => 1,
            'school_id' => $request->school_id,
        ]);

        if ($request->has('roles')) {
            // Monta array com school_id junto
            $rolesSync = [];
            foreach ($request->roles ?? [] as $role_id) {
                $rolesSync[$role_id] = ['school_id' => $request->school_id];
            }

            // Salva as roles vinculadas
            $usuario->roles()->sync($rolesSync);

            //$usuario->roles()->sync($request->roles);
        }

        return redirect()->route('secretaria.usuarios.index')->with('success', 'Usuário criado!');
    }

    public function update(Request $request, Usuario $usuario)
    {
        $secretaria = auth()->user()->escola;

        if (!$secretaria->filhas->contains($usuario->school_id)) {
            return redirect()->route('secretaria.usuarios.index')->with('error','Usuário não permitido.');
        }

        $usuario->update([
            'nome_u' => $request->nome_u,
            'cpf' => $request->cpf,
            'school_id' => $request->school_id,
        ]);

        if ($request->filled('senha')) {
            $usuario->update(['senha_hash' => Hash::make($request->senha)]);
        }

        // No store e no update, antes de sync():
        $rolesValidos = Role::whereNotIn('role_name', ['master', 'secretaria'])
                    ->pluck('id')
                    ->toArray();

        $rolesSelecionadas = $request->roles ?? [];
        $rolesFiltradas = array_intersect($rolesSelecionadas, $rolesValidos);

        //não deixa salvar roles proibidos para secretaria
        $usuario->roles()->sync($rolesFiltradas);

        return redirect()->route('secretaria.usuarios.index')->with('success', 'Usuário atualizado!');
    }*/

    public function edit(Usuario $usuario)
    {
        $secretaria = auth()->user()->escola;

        if (!$secretaria) {
            return redirect()->route('home')->with('error', 'Nenhuma secretaria vinculada.');
        }

        if (!$secretaria->filhas->contains($usuario->school_id)) {
            return redirect()->route('secretaria.usuarios.index')->with('error','Usuário não permitido.');
        }

        // secretária e suas filhas
        $filhas = $secretaria->filhas()->get();
        $escolas = collect([$secretaria])->merge($filhas);

        //$escolas = $secretaria->filhas;
        //$roles = Role::where('role_name', '!=', 'master')->get();
        
        // filtrar roles (sem master e secretaria)
        $roles = Role::whereNotIn('role_name', ['master', 'secretaria'])->get();


        return view('secretaria.usuarios.edit', compact('usuario','escolas','roles','secretaria'));
    }

    public function destroy(Usuario $usuario)
    {
        $auth = auth()->user();
        $currentSchoolId = session('current_school_id');
        $secretaria = $auth->escola;

        // 🧱 1️⃣ Impede autoexclusão da role secretaria ativa
        $isSelfSecretaria = $usuario->id === $auth->id &&
            $usuario->roles()
                ->where('role_name', 'secretaria')
                ->wherePivot('school_id', $currentSchoolId)
                ->exists();

        if ($isSelfSecretaria) {
            return back()->with('error', '🚫 Você não pode excluir sua própria role de Secretaria ativa.');
        }

        // 🧱 2️⃣ Impede excluir colegas secretários da mesma secretaria
        $isColegaSecretaria = $usuario->roles()
            ->where('role_name', 'secretaria')
            ->wherePivot('school_id', $currentSchoolId)
            ->exists();

        if ($isColegaSecretaria) {
            return back()->with('error', '🚫 Você não pode excluir um colega de Secretaria nesta unidade.');
        }

        // 🧱 3️⃣ Garante que o vínculo pertence a uma escola da secretaria logada
        $filhasIds = $secretaria->filhas()->pluck('id')->toArray();
        $permitidos = array_merge([$secretaria->id], $filhasIds);

        // Se o vínculo for externo → negar
        $vinculosDoUsuario = $usuario->roles()->pluck(prefix('usuario_role') . '.school_id')->toArray();
        if (!array_intersect($permitidos, $vinculosDoUsuario)) {
            return back()->with('error', '🚫 Usuário não permitido para exclusão nesta Secretaria.');
        }

        // 🧱 4️⃣ Exclui apenas o vínculo da escola ativa
        try {
            $usuario->roles()->wherePivot('school_id', $currentSchoolId)->detach();
        } catch (\Illuminate\Database\QueryException $e) {
            // Se houver FK constraint → erro amigável
            if (str_contains($e->getMessage(), '23000')) {
                return back()->with('error', '⚠️ Este vínculo não pode ser removido porque está em uso (referenciado em outras tabelas).');
            }
            throw $e; // outro erro desconhecido
        }

        // 🧱 5️⃣ Se não tiver mais vínculos, pode excluir completamente o usuário
        $aindaTemVinculos = $usuario->roles()->exists();

        if (! $aindaTemVinculos) {
            try {
                $usuario->delete();
                return redirect()->route('secretaria.usuarios.index')->with('success', '🗑️ Usuário e seus vínculos removidos com sucesso.');
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), '23000')) {
                    return back()->with('error', '⚠️ O usuário não pode ser excluído porque há registros dependentes.');
                }
                throw $e;
            }
        }

        return redirect()
            ->route('secretaria.usuarios.index')
            ->with('success', '🔗 Vínculo do usuário removido com sucesso.');
    }


    /*public function destroy(Usuario $usuario)
    {
        $secretaria = auth()->user()->escola;

        if (!$secretaria->filhas->contains($usuario->school_id)) {
            return redirect()->route('secretaria.usuarios.index')->with('error','Usuário não permitido.');
        }

        // Remove os vínculos na tabela pivot primeiro
        $usuario->roles()->detach();

        // Agora pode excluir o usuário
        $usuario->delete();

        return redirect()->route('secretaria.usuarios.index')->with('success', 'Usuário excluído!');
    }*/


}



/*
<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        // secretaria logada
        $secretaria = auth()->user()->escola;

        if (!$secretaria) {
            return redirect()->route('home')->with('error', 'Nenhuma secretaria vinculada a este usuário.');
        }

        // busca usuários vinculados a escolas filhas da secretaria
        $usuarios = Usuario::with(['escola', 'roles'])
            ->whereHas('escola', function ($q) use ($secretaria) {
                $q->where('secretaria_id', $secretaria->id);
            })
            ->get();

        return view('secretaria.usuarios.index', compact('usuarios'));
    }

    /*public function index()
    {
        $secretaria = Auth::user()->escola;
        $escolasIds = Escola::where('secretaria_id', $secretaria->id)->pluck('id')->push($secretaria->id);

        $usuarios = Usuario::whereIn('school_id', $escolasIds)
            ->with(['escola','roles'])
            ->get();

        return view('secretaria.usuarios.index', compact('usuarios','secretaria'));
    }/

    public function create()
    {
        $secretaria = Auth::user()->escola;
        $escolas = Escola::where('secretaria_id',$secretaria->id)->orWhere('id',$secretaria->id)->get();
        $roles = Role::all();

        return view('secretaria.usuarios.create', compact('escolas','roles'));
    }

    public function store(Request $request)
    {
        $usuario = Usuario::create([
            'nome_u' => $request->nome_u,
            'cpf'    => $request->cpf,
            'senha_hash' => Hash::make($request->senha),
            'school_id'  => $request->school_id,
            'status'     => 1,
        ]);

        if ($request->roles) {
            foreach($request->roles as $role_id) {
                $usuario->roles()->attach($role_id, ['school_id'=>$request->school_id]);
            }
        }

        return redirect()->route('secretaria.usuarios.index')->with('success','Usuário criado');
    }

    public function edit(Usuario $usuario)
    {
        $secretaria = Auth::user()->escola;
        $escolas = Escola::where('secretaria_id',$secretaria->id)->orWhere('id',$secretaria->id)->get();
        $roles = Role::all();

        return view('secretaria.usuarios.edit', compact('usuario','escolas','roles'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $usuario->update($request->only('nome_u','cpf','school_id','status'));

        if ($request->filled('senha')) {
            $usuario->update(['senha_hash'=>Hash::make($request->senha)]);
        }

        $usuario->roles()->sync([]);
        if ($request->roles) {
            foreach($request->roles as $role_id) {
                $usuario->roles()->attach($role_id, ['school_id'=>$request->school_id]);
            }
        }

        return redirect()->route('secretaria.usuarios.index')->with('success','Usuário atualizado');
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('secretaria.usuarios.index')->with('success','Usuário excluído');
    }
}
*/