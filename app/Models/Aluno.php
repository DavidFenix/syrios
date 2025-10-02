<?php

namespace App\Models;

class Aluno extends BaseModel
{   

    protected $basename = 'aluno'; // vira syrios_aluno
    protected $primaryKey = 'id';

    protected $fillable = [
        'matricula',
        'school_id',
        'nome_a',
    ];

    // Relacionamento: um aluno pertence a uma escola
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'school_id');
    }
}
