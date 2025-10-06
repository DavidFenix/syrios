<?php

namespace Database\Factories;

use App\Models\Disciplina;
use App\Models\Escola;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisciplinaFactory extends Factory
{
    protected $model = Disciplina::class;

    public function definition()
    {
        return [
            'abr'       => strtoupper($this->faker->lexify('DISC?')),
            'descr_d'   => $this->faker->sentence(3),
            'school_id' => Escola::inRandomOrder()->first()?->id ?? Escola::factory(),
        ];
    }
}
