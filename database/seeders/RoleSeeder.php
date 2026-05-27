<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nama_role' => 'user',        'deskripsi' => 'Pelaku usaha, hanya mengelola data pendukung produk miliknya'],
            ['nama_role' => 'admin',       'deskripsi' => 'Pengurus operasional, kelola produk, import, verifikasi, dan user pelaku usaha'],
            ['nama_role' => 'super_admin', 'deskripsi' => 'Pengelola utama, kelola admin, hak akses, log, audit, dan konfigurasi sistem'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['nama_role' => $role['nama_role']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
