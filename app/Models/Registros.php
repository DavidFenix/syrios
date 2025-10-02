<?php

namespace App\Models;

class Registros extends BaseModel
{
    protected $basename   = 'registros'; // syrios_registros
    public    $timestamps = false;

    protected $fillable = [
        'school_id',
        'descr_r',
    ];

    public function escola()      { return $this->belongsTo(Escola::class, 'school_id'); }
    public function ocorrencias() { return $this->hasMany(Ocorrencia::class, 'registro_id'); }
}
