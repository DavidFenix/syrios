<?php

namespace App\Models;

class Turma extends BaseModel
{
    protected $basename = 'turma';

    protected $fillable = [
        'serie_turma',
        'turno',
        'school_id',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'school_id');
    }
}
