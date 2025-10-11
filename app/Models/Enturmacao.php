<?php

namespace App\Models;

class Enturmacao extends BaseModel
{
    protected $basename   = 'enturmacao'; // syrios_enturmacao
    
    protected $fillable = [
        'school_id',
        'aluno_id',
        'turma_id',
    ];

    public function escola() { return $this->belongsTo(Escola::class, 'school_id'); }
    public function aluno()  { return $this->belongsTo(Aluno::class, 'aluno_id'); }
    public function turma()  { return $this->belongsTo(Turma::class, 'turma_id'); }
}
