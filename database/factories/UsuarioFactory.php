<?php

namespace Database\Factories;

use App\Models\Usuario;
use App\Models\Escola;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        return [
            'school_id'       => Escola::inRandomOrder()->first()?->id ?? Escola::factory(),
            'cpf'             => $this->faker->unique()->numerify('###########'),
            'senha_hash'      => bcrypt('123456'),
            'nome_u'          => $this->faker->name(),
            'status'          => 1,
            'is_super_master' => 0,
        ];
    }
}
