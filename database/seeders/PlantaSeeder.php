<?php

namespace Database\Seeders;

use App\Models\Planta;
use App\Models\Cliente;
use Illuminate\Database\Seeder;

class PlantaSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();

        foreach ($clientes as $cliente) {
            Planta::factory()->count(rand(1, 3))->create([
                'cliente_id' => $cliente->id,
            ]);
        }
    }
}

