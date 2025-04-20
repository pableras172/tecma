<?php 
namespace Database\Seeders;
use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        $json = file_get_contents(storage_path('app/provincias.json'));
        $provincias = json_decode($json, true);

        foreach ($provincias as $provincia) {
            Province::create([
                'country_id' => 1, // España
                'name' => $provincia['label'],
                'id' => (int)$provincia['code'], // si prefieres usar el mismo ID del JSON
            ]);
        }
    }
}
