<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    use LogsAuditTrail;

    public function index(Request $request): JsonResponse
    {
        $users = User::with('role')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');
                $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($request->filled('role'), fn ($query) => $query->whereHas('role', fn ($q) => $q->where('nama_role', $request->query('role'))))
            ->orderBy('nama')
            ->paginate($request->query('per_page', 20));

        return response()->json($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
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

        return response()->json(['message' => 'User berhasil dibuat.', 'data' => $user->load('role')], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user->load('role')]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        if ($user->id === auth()->id() || ($user->role->nama_role ?? null) === 'super_admin') {
            return response()->json(['message' => 'Akun ini tidak boleh diubah dari endpoint user management.'], 403);
        }

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

        return response()->json(['message' => 'User berhasil diperbarui.', 'data' => $user->fresh('role')]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id() || ($user->role->nama_role ?? null) === 'super_admin') {
            return response()->json(['message' => 'Akun ini tidak boleh dihapus.'], 403);
        }

        $before = $user->load('role')->toArray();
        $user->delete();
        $this->logAudit('delete', 'users', $before['id'], $before, null);

        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}
