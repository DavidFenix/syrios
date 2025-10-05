<?php

namespace App\Models;

class Escola extends BaseModel
{
    protected $basename = 'escola'; // vira syrios_escola
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'inep',
        'cnpj',
        'nome_e',
        'cidade',
        'estado',
        'endereco',
        'telefone',
        'secretaria_id',
        // 'is_master' 👈 não incluímos por segurança
    ];

    protected $casts = [
        'is_master' => 'boolean', // 👈 converte automaticamente para true/false
    ];

    // 🔎 Novo escopo de filtro
    public function scopeFiltrar($query, ?string $tipo)
    {
        if ($tipo === 'mae') {
            $query->whereNull('secretaria_id');
        } elseif ($tipo === 'filha') {
            $query->whereNotNull('secretaria_id');
        }
        return $query;
    }

    // Auto-relacionamento: Secretaria
    public function secretaria()
    {
        return $this->belongsTo(Escola::class, 'secretaria_id');
    }

    // Relacionamentos principais
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'school_id');
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class, 'school_id');
    }

    public function professores()
    {
        return $this->hasMany(Professor::class, 'school_id');
    }

    public function disciplinas()
    {
        return $this->hasMany(Disciplina::class, 'school_id');
    }

    // Uma escola filha pertence a uma escola mãe
    public function maes()
    {
        return $this->belongsTo(Escola::class, 'secretaria_id');
    }

    // Uma escola filha pertence a uma escola mãe
    public function mae()
    {
        return $this->belongsTo(Escola::class, 'secretaria_id');
    }

    // Uma escola mãe tem várias escolas filhas
    public function filhas()
    {
        return $this->hasMany(Escola::class, 'secretaria_id');
    }
}

