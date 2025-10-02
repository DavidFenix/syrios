<?php

namespace App\Models;

class Disciplina extends BaseModel
{
    protected $basename = 'disciplina';

    protected $fillable = [
        'abr',
        'descr_d',
        'school_id',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'school_id');
    }
}
