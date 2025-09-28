<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Prefixo centralizado
        // $prefix = config('database.prefix', 'syrios_');

        // if (!isset($this->table) && property_exists($this, 'basename')) {
        //     $this->table = $prefix . $this->basename;
        // }

        // se a classe define $basename, aplica o prefixo do config
        if (!isset($this->table) && property_exists($this, 'basename')) {
            $prefix = config('prefix.tabelas', 'syrios_');
            $this->table = $prefix . $this->basename;
        }
    }
}
