<?php

namespace App\Models;

class Ocorrencia extends BaseModel
{
    protected $basename   = 'ocorrencia'; // syrios_ocorrencia
    public    $timestamps = false;        // não usa created_at/updated_at padrão

    protected $fillable = [
        'school_id',
        'professor_id',
        'aluno_id',
        'oferta_id',
        'registro_id',
        'status_id',
        'data_ocorrencia',
        'descricao',
        'local',
        'atitude',
        'outra_acoes',
        'comportamento',
        'medidas',
        'encaminhamento',
        'recebido_em',
        'sync',
        'criado_em',
        'atualizado_em',
    ];

    protected $casts = [
        'data_ocorrencia' => 'datetime',
        'recebido_em'     => 'datetime',
        'criado_em'       => 'datetime',
        'atualizado_em'   => 'datetime',
        'sync'            => 'boolean',
    ];

    public function escola()    { return $this->belongsTo(Escola::class, 'school_id'); }
    public function professor() { return $this->belongsTo(Professor::class, 'professor_id'); }
    public function aluno()     { return $this->belongsTo(Aluno::class, 'aluno_id'); }
    public function oferta()    { return $this->belongsTo(Oferta::class, 'oferta_id'); }
    public function registro()  { return $this->belongsTo(Registros::class, 'registro_id'); }
    public function status()    { return $this->belongsTo(RegStatus::class, 'status_id'); }
}
