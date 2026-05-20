<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StateSeeder::class,
            CitySeeder::class,
            BrandSeeder::class,
            VehicleModelSeeder::class,
            StoreSeeder::class,
            UserSeeder::class,
            VehicleSeeder::class,
            AdvancedFeaturesSeeder::class,
            PostSeeder::class,
        ]);
    }
}
