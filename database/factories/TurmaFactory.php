<?php

namespace Database\Factories;

use App\Models\Turma;
use App\Models\Escola;
use Illuminate\Database\Eloquent\Factories\Factory;

class TurmaFactory extends Factory
{
    protected $model = Turma::class;

    public function definition()
    {
        return [
            'school_id'   => Escola::inRandomOrder()->first()?->id ?? Escola::factory(),
            'serie_turma' => 'Turma ' . $this->faker->randomDigitNotZero(),
            'turno'       => $this->faker->randomElement(['manhã', 'tarde', 'integral']),
        ];
    }
}
