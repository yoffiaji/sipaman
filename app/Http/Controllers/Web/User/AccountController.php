<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('user.settings.index', compact('user'));
    }

    public function updateNama(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ], [
            'nama.required' => 'Nama tidak boleh kosong.',
        ]);

        $user = Auth::user();
        $user->nama = $request->nama;
        $user->save();

        return back()->with('success_nama', 'Nama berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama'              => ['required'],
            'password_baru'              => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password_lama.required'     => 'Password lama wajib diisi.',
            'password_baru.required'     => 'Password baru wajib diisi.',
            'password_baru.min'          => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->password = Hash::make($request->password_baru);
        $user->save();

        return back()->with('success_password', 'Password berhasil diperbarui.');
    }
}