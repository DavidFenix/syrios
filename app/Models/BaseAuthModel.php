<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class BaseAuthModel extends Authenticatable
{
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // se a classe define $basename, aplica o prefixo do config
        if (!isset($this->table) && property_exists($this, 'basename')) {
            $prefix = config('prefix.tabelas', '');
            $this->table = $prefix . $this->basename;
        }
    }
}


?>