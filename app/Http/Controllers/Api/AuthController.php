<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use LogsAuditTrail;

    // ──────────────────────────────────────────────────────────
    //  POST /api/auth/register  (Publik) — DINONAKTIFKAN
    // ──────────────────────────────────────────────────────────
    public function register(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Registrasi mandiri dinonaktifkan. Akun pelaku usaha dibuat oleh admin PIRT.',
        ], 403);
    }

    // ──────────────────────────────────────────────────────────
    //  POST /api/auth/login  (Publik)
    //  Body: { "identifier": "<nib or email>", "password": "..." }
    //
    //  identifier diterima sebagai NIB (angka) ATAU email.
    //  - Admin/super_admin: pakai email
    //  - Pelaku usaha: pakai NIB
    // ──────────────────────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
        ]);

        $identifier = trim($request->identifier);

        $user = User::with('role')
            ->where(function ($q) use ($identifier) {
                $q->where('email', $identifier)
                  ->orWhere('nib', $identifier);
            })
            ->first();

        // Pesan generik untuk identifier yang tidak ditemukan — jangan
        // bocorkan apakah email/NIB-nya valid (security best practice).
        if (! $user) {
            throw ValidationException::withMessages([
                'identifier' => ['Email/NIB atau password salah.'],
            ]);
        }

        // Akun pelaku usaha yang baru auto-create dari import: password masih null.
        // Pesan khusus supaya pelaku usaha tahu harus minta password ke admin.
        if ($user->needsPasswordSetup()) {
            return response()->json([
                'message'              => 'Akun Anda belum diaktifkan. Silakan minta password ke admin PIRT.',
                'needs_password_setup' => true,
            ], 403);
        }

        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['Email/NIB atau password salah.'],
            ]);
        }

        if ($user->status_akun !== 'aktif') {
            return response()->json([
                'message' => 'Akun Anda '.$user->status_akun.'. Silakan hubungi administrator.',
            ], 403);
        }

        // Hapus token lama supaya tidak menumpuk
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        $logIdentifier = $user->email ?? $user->nib ?? "user#{$user->id}";
        $this->logActivity("Login berhasil: {$logIdentifier}", $user->id);

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => $this->formatUser($user),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    //  POST /api/auth/logout  (Auth)
    // ──────────────────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $logIdentifier = $user->email ?? $user->nib ?? "user#{$user->id}";
        $this->logActivity("Logout: {$logIdentifier}");
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    // ──────────────────────────────────────────────────────────
    //  GET /api/auth/me  (Auth)
    // ──────────────────────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->formatUser($request->user()->load('role')),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    //  POST /api/auth/update-profile  (Auth)
    // ──────────────────────────────────────────────────────────
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'nama'     => 'sometimes|string|max:150',
            'email'    => "sometimes|nullable|email|max:150|unique:users,email,{$user->id}",
            'password' => ['sometimes', 'confirmed', Password::min(8)],
        ]);

        $sebelum = $user->only(['nama', 'email']);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        $this->logAudit('update', 'users', $user->id, $sebelum, $user->fresh()->only(['nama', 'email']));

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $this->formatUser($user->fresh()->load('role')),
        ]);
    }

    // ── Private Helper ────────────────────────────────────────
    private function formatUser(User $user): array
    {
        return [
            'id'          => $user->id,
            'nama'        => $user->nama,
            'email'       => $user->email,
            'nib'         => $user->nib,
            'role'        => $user->role->nama_role ?? null,
            'status_akun' => $user->status_akun,
        ];
    }
}
