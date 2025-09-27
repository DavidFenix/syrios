<?php

namespace App\Models;

use App\Models\BaseModel;

class Role extends BaseModel
{
    protected $basename = 'role';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['role_name'];

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            config('prefix.tabelas') . 'usuario_role',
            'role_id',
            'usuario_id'
        )->withPivot('school_id');
    }
}
