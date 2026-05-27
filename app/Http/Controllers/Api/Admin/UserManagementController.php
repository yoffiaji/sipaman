<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AuditTrail;
use App\Models\Role;
use App\Models\User;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * UserManagementController
 * ------------------------
 * Akses admin & super_admin : CRUD user, toggle status, set password
 * Akses super_admin only    : updateRole, activityLogs, auditTrails
 */
class UserManagementController extends Controller
{
    use LogsAuditTrail;

    // ── GET /api/admin/users ──────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $query = User::with('role');
        $authRole = auth()->user()->role->nama_role ?? null;

        if ($authRole === 'admin') {
            $query->whereHas('role', fn ($q) => $q->where('nama_role', 'user'));
        }

        if ($s = $request->query('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('nib', 'like', "%{$s}%");
            });
        }

        if ($role = $request->query('role')) {
            $query->whereHas('role', fn ($q) => $q->where('nama_role', $role));
        }

        if ($status = $request->query('status_akun')) {
            $query->where('status_akun', $status);
        }

        // Filter khusus: hanya tampilkan user yang belum punya password
        // (untuk tab "Aktivasi Akun Pelaku Usaha" di dashboard admin)
        if ($request->boolean('needs_password_setup')) {
            $query->whereNull('password');
        }

        return response()->json(
            $query->orderBy('nama')->paginate($request->query('per_page', 20))
        );
    }

    // ── GET /api/admin/users/{user} ───────────────────────────
    public function show(User $user): JsonResponse
    {
        if ($response = $this->guardAdminCanManageOnlyPelakuUsaha($user)) {
            return $response;
        }

        return response()->json([
            'data' => $user->load('role'),
            'needs_password_setup' => $user->needsPasswordSetup(),
        ]);
    }

    // ── POST /api/admin/users ─────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $authRole = auth()->user()->role->nama_role;

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:users,email',
            'nib' => 'nullable|string|max:50|unique:users,nib',
            'password' => ['nullable', Password::min(8)],
            'role' => 'required|in:user,admin,super_admin',
            'status_akun' => 'nullable|in:aktif,nonaktif,kunci',
        ]);

        if ($authRole === 'admin' && $data['role'] !== 'user') {
            return response()->json([
                'message' => 'Admin hanya diizinkan membuat akun user/pelaku usaha.',
            ], 403);
        }

        if ($data['role'] === 'super_admin') {
            return response()->json([
                'message' => 'Akun super_admin tidak boleh dibuat dari manajemen user.',
            ], 403);
        }

        // Untuk role user, minimal NIB harus ada (karena login pakai NIB)
        if ($data['role'] === 'user' && empty($data['nib'])) {
            return response()->json([
                'message' => 'Akun pelaku usaha wajib memiliki NIB sebagai identifier login.',
            ], 422);
        }

        // Untuk role admin/super_admin, minimal email harus ada
        if (in_array($data['role'], ['admin', 'super_admin']) && empty($data['email'])) {
            return response()->json([
                'message' => 'Akun admin wajib memiliki email sebagai identifier login.',
            ], 422);
        }

        $role = Role::where('nama_role', $data['role'])->firstOrFail();

        $user = User::create([
            'nama' => $data['nama'],
            'email' => $data['email'] ?? null,
            'nib' => $data['nib'] ?? null,
            'password' => isset($data['password']) ? bcrypt($data['password']) : null,
            'role_id' => $role->id,
            'status_akun' => $data['status_akun'] ?? 'aktif',
        ]);

        $this->logAudit('create', 'users', $user->id, null, [
            'nama' => $user->nama,
            'email' => $user->email,
            'nib' => $user->nib,
            'role' => $data['role'],
            'has_password' => isset($data['password']),
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat.'
                .($user->needsPasswordSetup() ? ' Password belum di-set — user belum bisa login.' : ''),
            'data' => $user->load('role'),
            'needs_password_setup' => $user->needsPasswordSetup(),
        ], 201);
    }

    // ── PUT /api/admin/users/{user} ───────────────────────────
    public function update(Request $request, User $user): JsonResponse
    {
        if ($response = $this->guardManageableAccount($user)) {
            return $response;
        }

        $data = $request->validate([
            'nama' => 'sometimes|string|max:150',
            'email' => "sometimes|nullable|email|max:150|unique:users,email,{$user->id}",
            'nib' => "sometimes|nullable|string|max:50|unique:users,nib,{$user->id}",
            'password' => ['sometimes', Password::min(8)],
            'status_akun' => 'sometimes|in:aktif,nonaktif,kunci',
        ]);

        $sebelum = $user->only(['nama', 'email', 'nib', 'status_akun']);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);
        $this->logAudit('update', 'users', $user->id, $sebelum,
            $user->fresh()->only(['nama', 'email', 'nib', 'status_akun']));

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'data' => $user->fresh()->load('role'),
        ]);
    }

    // ── DELETE /api/admin/users/{user} ────────────────────────
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Tidak dapat menghapus akun sendiri.',
            ], 422);
        }

        if ($response = $this->guardManageableAccount($user)) {
            return $response;
        }

        $sebelum = $user->only(['nama', 'email', 'nib']);
        $user->delete();
        $this->logAudit('delete', 'users', $user->id, $sebelum, null);

        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    // ── PATCH /api/admin/users/{user}/toggle ──────────────────
    public function toggleActive(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'status_akun' => 'required|in:aktif,nonaktif,kunci',
        ]);

        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Tidak dapat mengubah status akun sendiri.',
            ], 422);
        }

        if ($response = $this->guardManageableAccount($user)) {
            return $response;
        }

        $sebelum = $user->status_akun;
        $user->update(['status_akun' => $data['status_akun']]);

        $this->logAudit('update', 'users', $user->id,
            ['status_akun' => $sebelum],
            ['status_akun' => $data['status_akun']]
        );

        return response()->json([
            'message' => "Status akun berhasil diubah menjadi '{$data['status_akun']}'.",
            'status_akun' => $user->status_akun,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    //  POST /api/admin/users/{user}/set-password
    //
    //  Admin set password untuk akun pelaku usaha yang baru auto-create
    //  dari import (password masih null). Mode:
    //  - mode=manual + password=<string>      → pakai password yang diketik admin
    //  - mode=generate                         → sistem generate password random
    //
    //  Response selalu menyertakan password mentah (plain) supaya admin
    //  bisa kasih ke pelaku usaha. Plain password TIDAK disimpan di DB
    //  (yang disimpan tetap hash). Admin wajib catat / kirim langsung.
    // ──────────────────────────────────────────────────────────
    public function setPassword(Request $request, User $user): JsonResponse
    {
        if ($response = $this->guardManageableAccount($user)) {
            return $response;
        }

        $data = $request->validate([
            'mode'     => 'required|in:manual,generate',
            'password' => 'required_if:mode,manual|nullable|string|min:8|max:50',
        ]);

        if ($data['mode'] === 'generate') {
            // 12 karakter random — campur huruf besar/kecil + angka.
            // Hindari karakter yang sering keliru dibaca (0/O, 1/l).
            $plainPassword = Str::password(12, letters: true, numbers: true, symbols: false, spaces: false);
        } else {
            $plainPassword = $data['password'];
        }

        $isFirstTimeSetup = $user->needsPasswordSetup();

        $user->update([
            'password' => bcrypt($plainPassword),
        ]);

        // Audit log — jangan simpan plain password
        $this->logAudit('update', 'users', $user->id,
            ['has_password' => ! $isFirstTimeSetup],
            ['has_password' => true, 'mode' => $data['mode']]
        );

        return response()->json([
            'message'     => $isFirstTimeSetup
                ? 'Password berhasil di-set. Pelaku usaha sekarang dapat login.'
                : 'Password berhasil di-reset.',
            'plain_password' => $plainPassword,
            'warning'        => 'Catat atau kirim password ini sekarang — password tidak akan ditampilkan lagi.',
            'user'           => [
                'id'   => $user->id,
                'nama' => $user->nama,
                'nib'  => $user->nib,
            ],
        ]);
    }

    // ── PATCH /api/super-admin/users/{user}/role ──────────────
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'role' => 'required|in:user,admin,super_admin',
        ]);

        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Tidak dapat mengubah role akun sendiri.',
            ], 422);
        }

        if (($user->role->nama_role ?? null) === 'super_admin' || $data['role'] === 'super_admin') {
            return response()->json([
                'message' => 'Role super_admin tidak boleh diubah atau dibuat dari endpoint ini.',
            ], 403);
        }

        $roleModel = Role::where('nama_role', $data['role'])->firstOrFail();
        $sebelum = $user->role->nama_role ?? null;

        $user->update(['role_id' => $roleModel->id]);

        $this->logAudit('update', 'users', $user->id,
            ['role' => $sebelum],
            ['role' => $data['role']]
        );

        return response()->json([
            'message' => "Role user berhasil diubah menjadi '{$data['role']}'.",
            'data' => $user->fresh()->load('role'),
        ]);
    }

    // ── GET /api/super-admin/activity-logs ───────────────────
    public function activityLogs(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user:id,nama,email,nib')
            ->orderByDesc('created_at');

        if ($uid = $request->query('user_id')) {
            $query->where('user_id', $uid);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($keyword = $request->query('search')) {
            $query->where('aktivitas', 'like', "%{$keyword}%");
        }

        return response()->json(
            $query->paginate($request->query('per_page', 30))
        );
    }

    // ── GET /api/super-admin/audit-trails ─────────────────────
    public function auditTrails(Request $request): JsonResponse
    {
        $query = AuditTrail::with('user:id,nama,email,nib')
            ->orderByDesc('created_at');

        if ($uid = $request->query('user_id')) {
            $query->where('user_id', $uid);
        }
        if ($aksi = $request->query('aksi')) {
            $query->where('aksi', $aksi);
        }
        if ($tabel = $request->query('tabel_terkait')) {
            $query->where('tabel_terkait', $tabel);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return response()->json(
            $query->paginate($request->query('per_page', 30))
        );
    }

    private function guardAdminCanManageOnlyPelakuUsaha(User $user): ?JsonResponse
    {
        $authRole = auth()->user()->role->nama_role ?? null;
        $targetRole = $user->loadMissing('role')->role->nama_role ?? null;

        if ($authRole === 'admin' && $targetRole !== 'user') {
            return response()->json([
                'message' => 'Admin hanya boleh mengelola akun user/pelaku usaha.',
            ], 403);
        }

        return null;
    }

    private function guardManageableAccount(User $user): ?JsonResponse
    {
        if ($response = $this->guardAdminCanManageOnlyPelakuUsaha($user)) {
            return $response;
        }

        if (($user->loadMissing('role')->role->nama_role ?? null) === 'super_admin') {
            return response()->json([
                'message' => 'Akun super_admin tidak boleh diubah, dikunci, atau dihapus dari endpoint ini.',
            ], 403);
        }

        return null;
    }
}
