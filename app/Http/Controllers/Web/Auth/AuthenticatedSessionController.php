<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use LogsAuditTrail;

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ]);

        $identifier = trim($credentials['identifier']);

        $user = $this->findUserByIdentifier($identifier);

        if (! $user) {
            throw ValidationException::withMessages([
                'identifier' => 'Identitas login atau password salah.',
            ]);
        }

        if ($user->needsPasswordSetup()) {
            throw ValidationException::withMessages([
                'identifier' => 'Akun Anda belum diaktifkan. Silakan minta password ke admin SIPAMAN.',
            ]);
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => 'Identitas login atau password salah.',
            ]);
        }

        if ($user->status_akun !== 'aktif') {
            throw ValidationException::withMessages([
                'identifier' => 'Akun Anda '.$user->status_akun.'. Silakan hubungi administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $this->logActivity('Login web berhasil - '.$this->activityIdentity($user), $user->id);

        return redirect()->intended($this->redirectPath($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            $this->logActivity('Logout web - '.$this->activityIdentity($user), $user->id);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectPath(User $user): string
    {
        return match ($user->role->nama_role ?? null) {
            'admin', 'super_admin' => route('admin.dashboard'),
            'user' => route('user.dashboard'),
            default => route('home'),
        };
    }

    private function findUserByIdentifier(string $identifier): ?User
    {
        return User::with('role')
            ->where(function ($query) use ($identifier) {
                $query->where(function ($userQuery) use ($identifier) {
                    $userQuery->where('nib', $identifier)
                        ->whereHas('role', fn ($roleQuery) => $roleQuery->where('nama_role', 'user'));
                })->orWhere(function ($adminQuery) use ($identifier) {
                    $adminQuery->where('email', $identifier)
                        ->whereHas('role', fn ($roleQuery) => $roleQuery->whereIn('nama_role', ['admin', 'super_admin']));
                });
            })
            ->first();
    }

    private function activityIdentity(User $user): string
    {
        $role = $user->role->nama_role ?? 'unknown';
        $identifier = $user->hasRole('user')
            ? 'NIB '.($user->nib ?? "user#{$user->id}")
            : 'email '.($user->email ?? "user#{$user->id}");

        return "role: {$role}, {$identifier}";
    }
}
