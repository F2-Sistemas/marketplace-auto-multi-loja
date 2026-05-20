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
        // Helper function to insert or update user
        $upsertUser = function (string $email, array $data) {
            $user = DB::table('users')->where('email', $email)->first();
            if ($user) {
                DB::table('users')->where('id', $user->id)->update(array_merge($data, [
                    'updated_at' => now(),
                ]));
                return $user->id;
            } else {
                return DB::table('users')->insertGetId(array_merge($data, [
                    'email' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        };

        // Helper function to insert or update store_user relation
        $upsertStoreUser = function (int $storeId, int $userId, string $role) {
            $relation = DB::table('store_users')
                ->where('store_id', $storeId)
                ->where('user_id', $userId)
                ->first();

            if ($relation) {
                DB::table('store_users')
                    ->where('store_id', $storeId)
                    ->where('user_id', $userId)
                    ->update([
                        'role' => $role,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('store_users')->insert([
                    'store_id' => $storeId,
                    'user_id' => $userId,
                    'role' => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        };

        // 1. Central Admin
        $upsertUser('admin@rederevenda.com', [
            'name' => 'Administrador Central',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 2. Fetch stores
        $storeNatal = DB::table('stores')->where('public_id', 'autocar-natal')->first();
        $storeSP = DB::table('stores')->where('public_id', 'sp-veiculos')->first();

        // 3. User tenant admins
        if ($storeNatal !== null) {
            $natalAdminId = $upsertUser('gerente.natal@autocar.com', [
                'name' => 'Gerente AutoCar Natal',
                'password' => Hash::make('secret123'),
                'role' => 'tenant_admin',
                'status' => 'active',
            ]);

            $upsertStoreUser($storeNatal->id, $natalAdminId, 'tenant_admin');

            $natalStaffId = $upsertUser('vendedor.natal@autocar.com', [
                'name' => 'Vendedor AutoCar Natal',
                'password' => Hash::make('secret123'),
                'role' => 'tenant_staff',
                'status' => 'active',
            ]);

            $upsertStoreUser($storeNatal->id, $natalStaffId, 'tenant_staff');
        }

        if ($storeSP !== null) {
            $spAdminId = $upsertUser('gerente.sp@spveiculos.com', [
                'name' => 'Gerente SP Veículos',
                'password' => Hash::make('secret123'),
                'role' => 'tenant_admin',
                'status' => 'active',
            ]);

            $upsertStoreUser($storeSP->id, $spAdminId, 'tenant_admin');
        }

        // 4. Regular Buyer
        $upsertUser('comprador@gmail.com', [
            'name' => 'Comprador Teste',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'status' => 'active',
        ]);
    }
}

