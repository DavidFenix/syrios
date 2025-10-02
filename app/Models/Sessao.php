<?php

namespace App\Models;

class Sessao extends BaseModel
{
    protected $basename   = 'sessao'; // syrios_sessao
    public    $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'school_id',
        'criado_em',
    ];

    protected $casts = [
        'criado_em' => 'datetime',
    ];

    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }
    public function escola()  { return $this->belongsTo(Escola::class, 'school_id'); }
}
