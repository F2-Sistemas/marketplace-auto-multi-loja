<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            'Toyota' => ['Corolla', 'Hilux', 'Yaris', 'Etios'],
            'Chevrolet' => ['Onix', 'Prisma', 'Tracker', 'S10', 'Cruze'],
            'Volkswagen' => ['Gol', 'Polo', 'T-Cross', 'Nivus', 'Saveiro'],
            'Fiat' => ['Uno', 'Mobi', 'Argo', 'Toro', 'Strada', 'Cronos'],
            'Ford' => ['Ka', 'Fiesta', 'EcoSport', 'Ranger'],
            'Honda' => ['Civic', 'Fit', 'HR-V', 'City'],
            'Hyundai' => ['HB20', 'Creta', 'Tucson', 'HB20S'],
        ];

        foreach ($models as $brandName => $modelList) {
            $brand = DB::table('brands')->where('name', $brandName)->first();

            if ($brand === null) {
                continue;
            }

            foreach ($modelList as $modelName) {
                DB::table('vehicle_models')->insertOrIgnore([
                    'brand_id' => $brand->id,
                    'name' => $modelName,
                    'slug' => Str::slug($modelName),
                    'type' => 'carro',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
