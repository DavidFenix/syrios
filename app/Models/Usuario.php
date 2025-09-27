<?php

namespace App\Models;

class Usuario extends BaseModel
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome_u',
        'cpf',
        'senha_hash',
        'status',
        'school_id'
    ];

    // Um usuário pertence a uma escola
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'school_id', 'id');
    }

    // Um usuário pode ter várias roles (com pivot school_id)
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            $this->prefix . 'usuario_role',
            'usuario_id',
            'role_id'
        )->withPivot('school_id');
    }

    // Se usuário também é professor
    public function professor()
    {
        return $this->hasOne(Professor::class, 'usuario_id', 'id');
    }
}
