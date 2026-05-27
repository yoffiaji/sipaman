<?php

namespace Tests\Feature;

use App\Models\JenisBarang;
use App\Models\Kecamatan;
use App\Models\Produk;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_is_disabled(): void
    {
        $this->postJson('/api/auth/register', [
            'nama' => 'Pelaku Usaha',
            'email' => 'pelaku@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertForbidden()
            ->assertJsonPath('message', 'Registrasi mandiri dinonaktifkan. Akun pelaku usaha dibuat oleh admin PIRT.');
    }

    public function test_guest_can_view_public_product_detail_without_sensitive_fields(): void
    {
        $produk = $this->createProduk([
            'nib' => '1234567890001',
            'no_hp' => '08123456789',
        ]);

        $response = $this->getJson("/api/produk/{$produk->id}")
            ->assertOk()
            ->assertJsonPath('data.nama_branding', 'Keripik Tempe')
            ->assertJsonPath('data.status_verifikasi', 'terverifikasi');

        $data = $response->json('data');

        $this->assertArrayNotHasKey('nib', $data);
        $this->assertArrayNotHasKey('no_hp', $data);
        $this->assertArrayNotHasKey('verifikasi', $data);
    }

    public function test_user_can_only_update_supporting_fields_on_owned_product(): void
    {
        $user = $this->createUserWithRole('user', 'pelaku@example.test');
        $produk = $this->createProduk(['user_id' => $user->id]);
        $produkLain = $this->createProduk([
            'no_sppirt' => 'PIRT-002',
            'nama_branding' => 'Sambal Bawang',
        ]);

        Sanctum::actingAs($user);

        $this->patchJson("/api/user/produk/{$produk->id}", [
            'harga' => 15000,
            'deskripsi' => 'Renyah dan gurih.',
            'alamat_toko' => 'Jl. Karanganyar No. 1',
        ])->assertOk()
            ->assertJsonPath('data.harga', 15000)
            ->assertJsonPath('data.deskripsi', 'Renyah dan gurih.');

        $this->assertDatabaseHas('produks', [
            'id' => $produk->id,
            'harga' => 15000,
            'deskripsi' => 'Renyah dan gurih.',
        ]);

        $this->patchJson("/api/user/produk/{$produk->id}", [
            'no_sppirt' => 'PIRT-DIUBAH',
        ])->assertStatus(422)
            ->assertJsonPath('field_dilarang.0', 'no_sppirt');

        $this->patchJson("/api/user/produk/{$produkLain->id}", [
            'harga' => 25000,
        ])->assertNotFound();
    }

    public function test_admin_only_creates_business_users_while_super_admin_can_create_admin(): void
    {
        Role::firstOrCreate(['nama_role' => 'user'], ['deskripsi' => 'Pelaku usaha']);
        $admin = $this->createUserWithRole('admin', 'admin@example.test');

        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/users', [
            'nama' => 'Admin Baru',
            'email' => 'admin-baru@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ])->assertForbidden();

        $this->postJson('/api/admin/users', [
            'nama' => 'Pelaku Baru',
            'email' => 'pelaku-baru@example.test',
            'password' => 'password123',
            'role' => 'user',
        ])->assertCreated();

        $superAdmin = $this->createUserWithRole('super_admin', 'super@example.test');

        Sanctum::actingAs($superAdmin);

        $this->postJson('/api/admin/users', [
            'nama' => 'Admin Operasional',
            'email' => 'admin-operasional@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ])->assertCreated();

        $this->postJson('/api/admin/users', [
            'nama' => 'Super Admin Kedua',
            'email' => 'super-kedua@example.test',
            'password' => 'password123',
            'role' => 'super_admin',
        ])->assertForbidden();
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
            'password' => 'password123',
            'role_id' => $role->id,
            'status_akun' => 'aktif',
        ]);
    }

    private function createProduk(array $attributes = []): Produk
    {
        $kecamatan = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Karanganyar',
            'kab_kota' => 'Karanganyar',
        ]);

        $jenisBarang = JenisBarang::firstOrCreate([
            'nama_jenis' => 'Makanan ringan',
        ]);

        return Produk::create(array_merge([
            'no_sppirt' => 'PIRT-001',
            'nama_branding' => 'Keripik Tempe',
            'kecamatan_id' => $kecamatan->id,
            'jenis_barang_id' => $jenisBarang->id,
            'nama_pelaku_usaha' => 'Sari',
            'alamat' => 'Karanganyar',
            'nama_toko' => 'Toko Sari',
            'alamat_toko' => 'Karanganyar',
            'harga' => 12000,
            'deskripsi' => 'Oleh-oleh lokal.',
            'tanggal_pengajuan' => '2026-01-01',
            'tanggal_verifikasi' => '2026-01-10',
            'masa_berlaku_pirt' => '2031-01-10',
            'status_oss' => 'Aktif',
            'is_verified' => true,
        ], $attributes));
    }
}
