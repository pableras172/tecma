<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run()
    {
        Country::updateOrCreate(
            ['iso2' => 'ES'], // clave única
            [
                'id' => 1, // opcional si quieres que sea siempre 1
                'name' => 'España',
            ]
        );
    }
}
