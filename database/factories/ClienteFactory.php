<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company,
            'descripcion' => $this->faker->catchPhrase,
            'direccion' => $this->faker->address,
            'city_id' => null, // puedes asociar una ciudad existente si quieres
            'telefono1' => $this->faker->phoneNumber,
            'telefono2' => $this->faker->phoneNumber,
            'contacto' => $this->faker->name,
            'email' => $this->faker->companyEmail,
            'logo' => null,
            'observaciones' => $this->faker->sentence,
        ];
    }
}

