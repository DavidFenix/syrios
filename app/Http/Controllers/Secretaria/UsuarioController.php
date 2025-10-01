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
    }

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
        $secretaria = auth()->user()->escola;

        if (!$secretaria->filhas->contains($usuario->school_id)) {
            return redirect()->route('secretaria.usuarios.index')->with('error','Usuário não permitido.');
        }

        // Remove os vínculos na tabela pivot primeiro
        $usuario->roles()->detach();

        // Agora pode excluir o usuário
        $usuario->delete();

        return redirect()->route('secretaria.usuarios.index')->with('success', 'Usuário excluído!');
    }
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