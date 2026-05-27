<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if (! $this->expectsJson($request)) {
                return redirect()->guest(route('login'));
            }

            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->status_akun !== 'aktif') {
            if (! $this->expectsJson($request)) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda '.$user->status_akun.'. Hubungi administrator.']);
            }

            return response()->json([
                'message' => 'Akun Anda '.$user->status_akun.'. Hubungi administrator.',
            ], 403);
        }

        $userRole = $user->role->nama_role ?? null;

        if (! in_array($userRole, $roles)) {
            if (! $this->expectsJson($request)) {
                abort(403);
            }

            return response()->json([
                'message' => 'Forbidden. Anda tidak memiliki hak akses untuk resource ini.',
                'required_roles' => $roles,
                'your_role' => $userRole,
            ], 403);
        }

        return $next($request);
    }

    private function expectsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->is('api/*');
    }
}
