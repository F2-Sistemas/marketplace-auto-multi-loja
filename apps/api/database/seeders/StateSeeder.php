<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name' => 'São Paulo', 'uf' => 'SP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rio Grande do Norte', 'uf' => 'RN', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('states')->insertOrIgnore($states);
    }
}
