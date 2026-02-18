<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\RolePosition;
use Illuminate\Support\Facades\DB;

class UsersRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama
        DB::table('role_user')->truncate();
        DB::table('role_positions')->truncate();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();

        // 1️⃣ Buat role
        $roles = [
            ['name' => 'ADMINISTRATOR', 'slug' => 'administrator'],
            ['name' => 'MENTERI', 'slug' => 'menteri'],
            ['name' => 'STAFF', 'slug' => 'staff'],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Ambil semua role
        $adminRole = Role::where('slug', 'administrator')->first();
        $ministerRole = Role::where('slug', 'menteri')->first();
        $staffRole = Role::where('slug', 'staff')->first();

        // 2️⃣ Buat posisi (role_positions)
        $positions = [
            ['position' => 'ADMINISTRATOR', 'role_id' => $adminRole->id],
            ['position' => 'MENTERI', 'role_id' => $ministerRole->id],
            ['position' => 'ADMINKOPERASI', 'role_id' => $staffRole->id],
        ];

        foreach ($positions as $posData) {
            RolePosition::create($posData);
        }

        // 3️⃣ Buat user contoh
        $users = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin Kementerian',
                'email' => 'admin@kemenkop.go.id',
                'password' => Hash::make('password123')
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Menteri Koperasi',
                'email' => 'menteri@kemenkop.go.id',
                'password' => Hash::make('password123')
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Staff Koperasi',
                'email' => 'staff@kemenkop.go.id',
                'password' => Hash::make('password123')
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            // Assign role sesuai urutan
            if (str_contains($user->email, 'admin')) {
                $user->roles()->attach($adminRole->id);
            } elseif (str_contains($user->email, 'menteri')) {
                $user->roles()->attach($ministerRole->id);
            } else {
                $user->roles()->attach($staffRole->id);
            }
        }
    }
}
