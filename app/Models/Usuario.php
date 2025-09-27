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
