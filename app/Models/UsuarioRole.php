<?php

namespace App\Models;

use App\Models\BaseModel;

class UsuarioRole extends BaseModel
{
    protected $basename = 'usuario_role';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'role_id',
        'school_id'
    ];
}
