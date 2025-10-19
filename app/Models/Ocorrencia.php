<?php

namespace App\Models;

class Ocorrencia extends BaseModel
{
    protected $basename = 'ocorrencia';

    protected $fillable = [
        'school_id',
        'ano_letivo',
        'vigente',
        'aluno_id',
        'professor_id',
        'oferta_id',
        'descricao',
        'local',
        'atitude',
        'outra_atitude',
        'comportamento',
        'sugestao',
        'status',
        'nivel_gravidade',
        'sync',
        'recebido_em',
        'encaminhamentos'
    ];

    protected $casts = [
        'vigente' => 'boolean',
        'ano_letivo' => 'integer',
        'status' => 'integer',
        'nivel_gravidade' => 'integer',
        'sync' => 'integer',
        'recebido_em' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */


    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id');
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    public function oferta()
    {
        return $this->belongsTo(Oferta::class, 'oferta_id');
    }

    public function motivos()
    {
        return $this->belongsToMany(
            ModeloMotivo::class,
            prefix('ocorrencia_motivo'),
            'ocorrencia_id',
            'modelo_motivo_id'
        )->withTimestamps();
    }
    
    /*
    |--------------------------------------------------------------------------
    | ⚙️ SCOPES ÚTEIS
    |--------------------------------------------------------------------------
    */

    // Filtra ocorrências do ano letivo atual
    public function scopeAnoAtual($query)
    {
        return $query->where('ano_letivo', session('ano_letivo_atual') ?? date('Y'));
    }

    // Filtra por escola ativa
    public function scopeDaEscolaAtual($query)
    {
        return $query->where('school_id', session('current_school_id'));
    }

    // Filtra apenas ocorrências ativas
    public function scopeAtivas($query)
    {
        return $query->where('status', 1);
    }

}

/*
    namespace App\Models;

    class Ocorrencia extends BaseModel
    {
        protected $basename = 'ocorrencia'; // syrios_ocorrencia

        // ✅ Agora o Laravel atualiza created_at e updated_at automaticamente
        public $timestamps = true;

        protected $fillable = [
            'school_id',
            'professor_id',
            'aluno_id',
            'oferta_id',
            'modelo_motivo_id',
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
            'ano_letivo',
            'vigente',
            // ❌ Removidos 'criado_em' e 'atualizado_em'
        ];

        protected $casts = [
            'data_ocorrencia' => 'datetime',
            'recebido_em'     => 'datetime',
            'created_at'      => 'datetime', // ✅ novos nomes padrão
            'updated_at'      => 'datetime',
            'sync'            => 'boolean',
            'vigente'         => 'boolean',
            'ano_letivo'      => 'integer',
        ];

        // 🔗 Relacionamentos
        public function escola()    { return $this->belongsTo(Escola::class, 'school_id'); }
        public function professor() { return $this->belongsTo(Professor::class, 'professor_id'); }
        public function aluno()     { return $this->belongsTo(Aluno::class, 'aluno_id'); }
        public function oferta()    { return $this->belongsTo(Oferta::class, 'oferta_id'); }
        public function modelo_motivo()  { return $this->belongsTo(ModeloMotivo::class, 'modelo_motivo_id'); }
        public function status()    { return $this->belongsTo(RegStatus::class, 'status_id'); }
    }


    /*
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
    */
