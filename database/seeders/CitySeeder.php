<?php 

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        $json = file_get_contents(storage_path('app/poblaciones.json'));
        $ciudades = json_decode($json, true);

        foreach ($ciudades as $ciudad) {
            City::create([
                'province_id' => (int)$ciudad['parent_code'],
                'name' => $ciudad['label'],
            ]);
        }
    }
}
