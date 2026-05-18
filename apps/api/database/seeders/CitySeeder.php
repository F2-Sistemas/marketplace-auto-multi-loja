<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sp = DB::table('states')->where('uf', 'SP')->first();
        $rn = DB::table('states')->where('uf', 'RN')->first();

        if ($sp !== null) {
            DB::table('cities')->insertOrIgnore([
                'state_id' => $sp->id,
                'name' => 'São Paulo',
                'slug' => 'sao-paulo',
                'ibge_code' => '3550308',
                'latitude' => -23.55052000,
                'longitude' => -46.63330800,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($rn !== null) {
            DB::table('cities')->insertOrIgnore([
                'state_id' => $rn->id,
                'name' => 'Natal',
                'slug' => 'natal',
                'ibge_code' => '2408102',
                'latitude' => -5.77925700,
                'longitude' => -35.20091600,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
