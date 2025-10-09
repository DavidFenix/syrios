<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    
    public function index()
    {
        $roles = Role::all();
        return view('master.roles.index', compact('roles'));
    }

    public function create() {
        abort(403, 'Criação de roles não permitida.');
    }

    public function store(Request $request) {
        abort(403, 'Criação de roles não permitida.');
    }

    public function edit(Role $role) {
        abort(403, 'Edição de roles não permitida.');
    }

    public function update(Request $request, Role $role) {
        abort(403, 'Edição de roles não permitida.');
    }

    public function destroy(Role $role) {
        abort(403, 'Exclusão de roles não permitida.');
    }

 /*
    public function create()
    {
        return view('master.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:50|unique:syrios_role,role_name',
        ]);

        Role::create($request->all());

        return redirect()->route('master.roles.index')
                         ->with('success', 'Role criada com sucesso!');
    }

    public function show($id)
    {
        //
    }

    public function edit(Role $role)
    {
        return view('master.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_name' => 'required|string|max:50|unique:syrios_role,role_name,' . $role->id,
        ]);

        $role->update($request->all());

        return redirect()->route('master.roles.index')
                         ->with('success', 'Role atualizada com sucesso!');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('master.roles.index')
                         ->with('success', 'Role excluída com sucesso!');
    }
    */
}

