<?php

namespace App\Models;

class DiretorTurma extends BaseModel
{
    protected $basename = 'diretor_turma';

    protected $fillable = [
        'usuario_id',
        'turma_id',
        'school_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'school_id');
    }
}
