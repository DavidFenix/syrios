<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $prefix;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // prefixo vem do config/prefix.php (ou direto aqui se preferir fixo)
        $this->prefix = config('prefix.tabelas', 'syrios_');

        // Se o model não definiu explicitamente sua tabela,
        // gera automaticamente com base no nome da classe em minúsculo
        if (!isset($this->table)) {
            $className = strtolower(class_basename($this)); // Usuario → usuario
            $this->table = $this->prefix . $className;
        }
    }

    public $timestamps = false; // suas tabelas não usam created_at/updated_at
}
