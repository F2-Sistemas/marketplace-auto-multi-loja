<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'public_id' => 'autocar-natal',
                'name' => 'AutoCar Natal',
                'slug' => 'autocar-natal',
                'status' => 'active',
                'domain' => 's1.app-loja.rederevenda.com',
            ],
            [
                'public_id' => 'sp-veiculos',
                'name' => 'São Paulo Veículos',
                'slug' => 'sp-veiculos',
                'status' => 'active',
                'domain' => 'sp-veiculos.rederevenda.com',
            ],
            [
                'public_id' => 'loja01',
                'name' => 'Loja 01 Multimarcas',
                'slug' => 'loja01',
                'status' => 'active',
                'domain' => 'loja01.rederevenda.com',
            ],
            [
                'public_id' => 'loja02',
                'name' => 'Loja 02 Premium',
                'slug' => 'loja02',
                'status' => 'active',
                'domain' => 'loja02.rederevenda.com',
            ],
            [
                'public_id' => 'tauro-motors',
                'name' => 'Tauro Motors',
                'slug' => 'tauro-motors',
                'status' => 'active',
                'domain' => 'tauro-motors.rederevenda.com',
            ],
        ];

        foreach ($stores as $storeData) {
            $storeId = DB::table('stores')->insertGetId([
                'public_id' => $storeData['public_id'],
                'name' => $storeData['name'],
                'slug' => $storeData['slug'],
                'status' => $storeData['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create internal domain
            DB::table('store_domains')->insert([
                'store_id' => $storeId,
                'domain' => $storeData['domain'],
                'type' => 'internal',
                'is_primary' => true,
                'is_verified' => true,
                'verified_at' => now(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add standard store settings
            DB::table('store_settings')->insert([
                [
                    'store_id' => $storeId,
                    'key' => 'logo_url',
                    'value' => 'https://api.rederevenda.com/images/default-logo.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'store_id' => $storeId,
                    'key' => 'whatsapp_number',
                    'value' => '5584999999999',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'store_id' => $storeId,
                    'key' => 'accent_color',
                    'value' => '#059669',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
