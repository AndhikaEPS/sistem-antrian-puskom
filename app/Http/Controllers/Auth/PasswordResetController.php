<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan form "Lupa Kata Sandi".
     * Catatan: karena sistem ini tidak menggunakan hashing password dan
     * belum terhubung ke layanan email sungguhan, reset dilakukan secara
     * langsung berdasarkan kecocokan email saja (tanpa link/token email).
     * Ini disederhanakan sesuai kebutuhan demo lokal/tugas kuliah.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di sistem kami.'])->onlyInput('email');
        }

        $user->update(['password' => $validated['password']]);

        return redirect()->route('login')->with('success', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru Anda.');
    }
}
