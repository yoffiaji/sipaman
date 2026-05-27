<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_nav_pages_are_reachable(): void
    {
        foreach ([
            '/',
            '/products',
            '/products/1',
            '/umkm',
            '/umkm/1',
            '/login',
        ] as $path) {
            $this->get($path)->assertOk();
        }

        $this->get('/register')->assertRedirect('/login');
    }

    public function test_guest_cannot_open_dashboards(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_nav_pages_are_reachable_for_admin(): void
    {
        $this->actingAs($this->createUserWithRole('admin', 'admin@example.test'));

        foreach ([
            '/admin',
            '/admin/products',
            '/admin/products/create',
            '/admin/products/1',
            '/admin/products/1/edit',
            '/admin/categories',
            '/admin/categories/create',
            '/admin/categories/1/edit',
            '/admin/umkm',
            '/admin/umkm/create',
            '/admin/umkm/1/edit',
            '/admin/verifications',
            '/admin/users',
            '/admin/users/create',
            '/admin/users/1/edit',
            '/admin/logs',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_web_login_redirects_by_role(): void
    {
        $this->createUserWithRole('super_admin', 'superadmin@pirt.go.id');

        $this->post('/login', [
            'email' => 'superadmin@pirt.go.id',
            'password' => 'password',
        ])->assertRedirect('/admin');
    }

    private function createUserWithRole(string $roleName, string $email): User
    {
        $role = Role::firstOrCreate(
            ['nama_role' => $roleName],
            ['deskripsi' => $roleName]
        );

        return User::create([
            'nama' => ucfirst(str_replace('_', ' ', $roleName)),
            'email' => $email,
            'password' => 'password',
            'role_id' => $role->id,
            'status_akun' => 'aktif',
        ]);
    }
}
