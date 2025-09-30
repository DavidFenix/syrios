<?php

namespace App\Models;

class Usuario extends BaseModel
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
}
