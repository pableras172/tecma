<?php

namespace Database\Factories;

use App\Models\Planta;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlantaFactory extends Factory
{
    protected $model = Planta::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Planta ' . $this->faker->word,
            'direccion' => $this->faker->address,
            'telefono1' => $this->faker->phoneNumber,
            'telefono2' => $this->faker->phoneNumber,
            'contacto' => $this->faker->name,
            'email' => $this->faker->email,
            'observaciones' => $this->faker->text(100),
            'cliente_id' => null, // se asigna en el seeder
        ];
    }
}

