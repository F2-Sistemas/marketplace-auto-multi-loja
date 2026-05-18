<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\City;
use App\Models\Store;
use App\Models\Vehicle;
use App\Models\VehicleFeature;
use App\Models\VehicleImage;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all();
        $cities = City::all();
        $brands = Brand::all();

        if ($stores->isEmpty() || $cities->isEmpty() || $brands->isEmpty()) {
            return;
        }

        $images = [
            'https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1583121274602-3e2820c69888?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1555215695-3004980ad54e?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1609521263047-f8f205293f24?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1617788138017-80ad40651399?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1592853625597-7d17be820d0c?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1611245801163-66f30d0857a1?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1614026480209-cd9ec340a4ab?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=800&auto=format&fit=crop',
        ];

        $featuresList = [
            'Ar Condicionado', 'Direção Hidráulica', 'Vidros Elétricos', 'Travas Elétricas',
            'Alarme', 'Freio ABS', 'Airbag', 'Rodas de Liga Leve', 'Bancos de Couro',
            'Central Multimídia', 'Teto Solar', 'Câmera de Ré', 'Sensor de Estacionamento',
        ];

        foreach ($stores as $store) {
            // Seed 8 vehicles per store
            for ($i = 0; $i < 8; $i++) {
                $brand = $brands->random();
                $model = VehicleModel::where('brand_id', $brand->id)->inRandomOrder()->first() ?? VehicleModel::factory()->create(['brand_id' => $brand->id]);
                $city = $cities->random();

                $fuel = fake()->randomElement(['flex', 'gasolina', 'diesel', 'eletrico']);
                $trans = fake()->randomElement(['automatico', 'manual']);
                $year = fake()->numberBetween(2018, 2025);

                $title = "{$brand->name} {$model->name} " . fake()->randomElement(['Flex Completo', 'Aut. Couro', 'Sport TSI', 'Turbo 4x4']);
                $slug = Str::slug($title) . '-' . fake()->numberBetween(100, 999);

                $vehicle = Vehicle::create([
                    'store_id' => $store->id,
                    'brand_id' => $brand->id,
                    'vehicle_model_id' => $model->id,
                    'city_id' => $city->id,
                    'title' => $title,
                    'slug' => $slug,
                    'version' => fake()->sentence(2),
                    'description' => fake()->paragraph(),
                    'price' => fake()->randomFloat(2, 49000, 240000),
                    'year_manufacture' => $year,
                    'year_model' => $year + fake()->randomElement([0, 1]),
                    'mileage' => fake()->numberBetween(5000, 110000),
                    'color' => fake()->randomElement(['Branco', 'Preto', 'Cinza', 'Prata', 'Azul', 'Vermelho']),
                    'transmission' => $trans,
                    'fuel' => $fuel,
                    'status' => 'published',
                    'views_count' => fake()->numberBetween(10, 450),
                ]);

                // Create 3-4 images for this vehicle
                $shuffledImages = $images;
                shuffle($shuffledImages);
                $imgCount = fake()->numberBetween(3, 5);

                for ($j = 0; $j < $imgCount; $j++) {
                    VehicleImage::create([
                        'vehicle_id' => $vehicle->id,
                        'path' => $shuffledImages[$j],
                        'is_featured' => $j === 0,
                        'order' => $j,
                    ]);
                }

                // Create 4-6 features for this vehicle
                $selectedFeatures = fake()->randomElements($featuresList, fake()->numberBetween(4, 7));
                foreach ($selectedFeatures as $featName) {
                    VehicleFeature::create([
                        'vehicle_id' => $vehicle->id,
                        'name' => $featName,
                    ]);
                }
            }
        }
    }
}
