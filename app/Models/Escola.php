<?php

namespace App\Models;

class Escola extends BaseModel
{
    protected $fillable = [
        'inep', 'cnpj', 'nome_e', 'cidade', 'estado',
        'endereco', 'telefone', 'secretaria_id'
    ];

    // Escola pode ter muitos usuários
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'school_id', 'id');
    }

    // Escola pode ter várias turmas
    public function turmas()
    {
        return $this->hasMany(Turma::class, 'school_id', 'id');
    }

    // Escola mãe → várias escolas filhas
    public function filhas()
    {
        return $this->hasMany(Escola::class, 'secretaria_id', 'id');
    }

    // Escola filha → pertence a uma escola mãe
    public function mae()
    {
        return $this->belongsTo(Escola::class, 'secretaria_id', 'id');
    }
}
