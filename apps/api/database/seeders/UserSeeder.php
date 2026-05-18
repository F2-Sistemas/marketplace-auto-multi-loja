<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Central Admin
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Administrador Central',
            'email' => 'admin@rederevenda.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Fetch stores
        $storeNatal = DB::table('stores')->where('public_id', 'autocar-natal')->first();
        $storeSP = DB::table('stores')->where('public_id', 'sp-veiculos')->first();

        // 3. User tenant admins
        if ($storeNatal !== null) {
            $natalAdminId = DB::table('users')->insertGetId([
                'name' => 'Gerente AutoCar Natal',
                'email' => 'gerente.natal@autocar.com',
                'password' => Hash::make('secret123'),
                'role' => 'tenant_admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('store_users')->insert([
                'store_id' => $storeNatal->id,
                'user_id' => $natalAdminId,
                'role' => 'tenant_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $natalStaffId = DB::table('users')->insertGetId([
                'name' => 'Vendedor AutoCar Natal',
                'email' => 'vendedor.natal@autocar.com',
                'password' => Hash::make('secret123'),
                'role' => 'tenant_staff',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('store_users')->insert([
                'store_id' => $storeNatal->id,
                'user_id' => $natalStaffId,
                'role' => 'tenant_staff',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($storeSP !== null) {
            $spAdminId = DB::table('users')->insertGetId([
                'name' => 'Gerente SP Veículos',
                'email' => 'gerente.sp@spveiculos.com',
                'password' => Hash::make('secret123'),
                'role' => 'tenant_admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('store_users')->insert([
                'store_id' => $storeSP->id,
                'user_id' => $spAdminId,
                'role' => 'tenant_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Regular Buyer
        DB::table('users')->insert([
            'name' => 'Comprador Teste',
            'email' => 'comprador@gmail.com',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
