<?php

namespace Database\Factories;

use App\Models\Aluno;
use App\Models\Escola;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlunoFactory extends Factory
{
    protected $model = Aluno::class;

    public function definition()
    {
        return [
            'matricula' => $this->faker->unique()->numerify('#####'),
            'school_id' => Escola::inRandomOrder()->first()?->id ?? Escola::factory(),
            'nome_a'    => $this->faker->name(),
        ];
    }
}
