<?php

namespace App\Models;

class ModeloMotivo extends BaseModel
{
    protected $basename   = 'modelo_motivo'; // syrios_registros
    
    protected $fillable = [
        'school_id',
        'descr_r',
    ];

    public function escola()      { return $this->belongsTo(Escola::class, 'school_id'); }
    public function ocorrencias() { return $this->hasMany(Ocorrencia::class, 'modelo_motivo_id'); }
}
