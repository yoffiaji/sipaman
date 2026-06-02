<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    use LogsAuditTrail;

    public function index(Request $request): View
    {
        $users = User::with('role')
            ->whereHas('role', fn ($query) => $query->where('nama_role', 'admin'))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');
                $query->where(fn ($q) => $q
                    ->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('super-admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::where('nama_role', 'admin')->firstOrFail();

        $user = User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'nib' => null,
            'password' => Hash::make($data['password']),
            'role_id' => $role->id,
            'status_akun' => $data['status_akun'] ?? 'aktif',
        ]);

        $this->logAudit('create', 'users', $user->id, null, $user->load('role')->toArray());

        return redirect()->route('super-admin.users.index')->with('success', 'Admin berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        abort_if($user->id === auth()->id(), 403, 'Akun sendiri tidak bisa diedit dari halaman ini.');
        abort_unless(($user->role->nama_role ?? null) === 'admin', 403, 'Halaman ini hanya untuk mengelola akun admin.');

        $user->load('role');

        return view('super-admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Akun sendiri tidak bisa diedit dari halaman ini.');
        abort_unless(($user->role->nama_role ?? null) === 'admin', 403, 'Halaman ini hanya untuk mengelola akun admin.');

        $before = $user->load('role')->toArray();
        $data = $request->validated();

        $updateData = [];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (array_key_exists('status_akun', $data)) {
            $updateData['status_akun'] = $data['status_akun'];
        }

        if ($updateData === []) {
            return back()->with('success', 'Tidak ada perubahan yang disimpan.');
        }

        $user->update($updateData);
        $this->logAudit('update', 'users', $user->id, $before, $user->fresh('role')->toArray());

        return redirect()->route('super-admin.users.index')->with('success', 'Credential/status akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Akun sendiri tidak bisa dihapus.');
        abort_if(($user->role->nama_role ?? null) === 'super_admin', 403, 'Akun super admin tidak bisa dihapus.');
        abort_unless(($user->role->nama_role ?? null) === 'admin', 403, 'Akun pelaku usaha tidak boleh dihapus. Nonaktifkan atau kunci akun jika perlu.');

        $before = $user->load('role')->toArray();
        $user->delete();
        $this->logAudit('delete', 'users', $before['id'], $before, null);

        return redirect()->route('super-admin.users.index')->with('success', 'Admin berhasil dihapus.');
    }
}
