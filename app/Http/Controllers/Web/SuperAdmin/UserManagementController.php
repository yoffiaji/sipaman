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
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');
                $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($request->filled('role'), fn ($query) => $query->whereHas('role', fn ($q) => $q->where('nama_role', $request->query('role'))))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::whereIn('nama_role', ['user', 'admin'])->orderBy('nama_role')->get();
        return view('super-admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::where('nama_role', $data['role'])->firstOrFail();

        $user = User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $role->id,
            'status_akun' => $data['status_akun'] ?? 'aktif',
        ]);

        $this->logAudit('create', 'users', $user->id, null, $user->load('role')->toArray());

        return redirect()->route('super-admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        abort_if($user->id === auth()->id(), 403, 'Akun sendiri tidak bisa diedit dari halaman ini.');
        abort_if(($user->role->nama_role ?? null) === 'super_admin', 403, 'Akun super admin tidak bisa diedit.');

        $roles = Role::whereIn('nama_role', ['user', 'admin'])->orderBy('nama_role')->get();
        $user->load('role');

        return view('super-admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Akun sendiri tidak bisa diedit dari halaman ini.');
        abort_if(($user->role->nama_role ?? null) === 'super_admin', 403, 'Akun super admin tidak bisa diedit.');

        $before = $user->load('role')->toArray();
        $data = $request->validated();

        if (isset($data['role'])) {
            $data['role_id'] = Role::where('nama_role', $data['role'])->firstOrFail()->id;
        }
        unset($data['role']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $this->logAudit('update', 'users', $user->id, $before, $user->fresh('role')->toArray());

        return redirect()->route('super-admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Akun sendiri tidak bisa dihapus.');
        abort_if(($user->role->nama_role ?? null) === 'super_admin', 403, 'Akun super admin tidak bisa dihapus.');

        $before = $user->load('role')->toArray();
        $user->delete();
        $this->logAudit('delete', 'users', $before['id'], $before, null);

        return redirect()->route('super-admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
