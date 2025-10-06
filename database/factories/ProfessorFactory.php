<?php

namespace Database\Factories;

use App\Models\Professor;
use App\Models\Usuario;
use App\Models\Escola;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessorFactory extends Factory
{
    protected $model = Professor::class;

    public function definition()
    {
        return [
            'usuario_id' => Usuario::inRandomOrder()->first()?->id ?? Usuario::factory(),
            'school_id'  => Escola::inRandomOrder()->first()?->id ?? Escola::factory(),
        ];
    }
}
