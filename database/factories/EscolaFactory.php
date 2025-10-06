<?php

namespace Database\Factories;

use App\Models\Escola;
use Illuminate\Database\Eloquent\Factories\Factory;

class EscolaFactory extends Factory
{
    protected $model = Escola::class;

    public function definition()
    {
        return [
            'inep'         => $this->faker->unique()->numerify('########'),
            'cnpj'         => $this->faker->unique()->numerify('##.###.###/####-##'),
            'nome_e'       => $this->faker->company(),
            'cidade'       => $this->faker->city(),
            'estado'       => $this->faker->stateAbbr(),
            'endereco'     => $this->faker->streetAddress(),
            'telefone'     => $this->faker->phoneNumber(),
            'criado_em'    => now(),
            'secretaria_id'=> null,
            'is_master'    => 0,
        ];
    }
}
