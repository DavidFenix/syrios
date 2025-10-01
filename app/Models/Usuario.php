<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends BaseAuthModel
{
    protected $basename = 'usuario';
    protected $primaryKey = 'id';

    protected $fillable = [
        'school_id',
        'cpf',
        'senha_hash',
        'nome_u',
        'status'
    ];

    //protected $hidden = ['senha_hash'];
    protected $hidden = [
        'senha_hash', 'remember_token',
    ];

    // Laravel espera "password"
    public function getAuthPassword()
    {
        return $this->senha_hash;
    }

    public function scopeFiltrarPorEscola($query, $filtro)
    {
        if ($filtro === 'mae') {
            // usuários vinculados a escolas que são secretarias
            return $query->whereHas('escola', function($q) {
                $q->whereNull('secretaria_id');
            });
        } elseif ($filtro === 'filha') {
            // usuários vinculados a escolas que são filhas
            return $query->whereHas('escola', function($q) {
                $q->whereNotNull('secretaria_id');
            });
        }
        return $query; // todos
    }


    // um Usuário pertence a uma escola
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'school_id');
    }

    // Usuário tem várias roles (multi-escola)
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'syrios_usuario_role',
            'usuario_id',
            'role_id'
        )->withPivot('school_id');
    }

    // App/Models/Usuario.php
    public function hasRole($roleName)
    {
        return $this->roles->contains('role_name', $roleName);
    }

}
