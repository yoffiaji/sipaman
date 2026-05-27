<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')
            ->whereIn('nama_role', ['super_admin', 'admin', 'user'])
            ->pluck('id', 'nama_role');

        if (! isset($roles['super_admin'], $roles['admin'], $roles['user'])) {
            throw new RuntimeException('Role super_admin, admin, atau user belum tersedia. Jalankan RoleSeeder dulu.');
        }

        $users = [
            [
                'nama' => 'Super Admin',
                'email' => env('SUPER_ADMIN_EMAIL', 'superadmin@pirt.go.id'),
                'password' => env('SUPER_ADMIN_PASSWORD', 'password'),
                'role_id' => $roles['super_admin'],
                'status_akun' => 'aktif',
            ],
            [
                'nama' => 'Admin',
                'email' => env('ADMIN_EMAIL', 'admin@pirt.go.id'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role_id' => $roles['admin'],
                'status_akun' => 'aktif',
            ],
            [
                'nama' => 'User',
                'email' => env('USER_EMAIL', 'user@pirt.go.id'),
                'password' => env('USER_PASSWORD', 'password'),
                'role_id' => $roles['user'],
                'status_akun' => 'aktif',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'nama' => $user['nama'],
                    'email' => $user['email'],
                    'password' => Hash::make($user['password']),
                    'role_id' => $user['role_id'],
                    'status_akun' => $user['status_akun'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}