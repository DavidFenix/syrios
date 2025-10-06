<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Aluno extends BaseModel
{   

    use HasFactory;
    
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
