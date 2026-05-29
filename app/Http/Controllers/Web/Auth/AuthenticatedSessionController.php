<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
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

        $user = User::with('role')
            ->where(function ($q) use ($identifier) {
                $q->where('email', $identifier)
                  ->orWhere('nib', $identifier);
            })
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'identifier' => 'Email/NIB atau password salah.',
            ]);
        }

        if ($user->needsPasswordSetup()) {
            throw ValidationException::withMessages([
                'identifier' => 'Akun Anda belum diaktifkan. Silakan minta password ke admin SIPAMAN.',
            ]);
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => 'Email/NIB atau password salah.',
            ]);
        }

        if ($user->status_akun !== 'aktif') {
            throw ValidationException::withMessages([
                'identifier' => 'Akun Anda '.$user->status_akun.'. Silakan hubungi administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
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
}
