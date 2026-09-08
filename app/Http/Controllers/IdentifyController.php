<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class IdentifyController extends Controller
{
    public function showMahasiswa()
    {
        return view('identify.mahasiswa');
    }

    public function identifyMahasiswa(Request $request): RedirectResponse
    {
        $validated = $request->validate(['nim' => ['required', 'string']]);
        $user = User::where('role', 'mahasiswa')->where('nim', $validated['nim'])->first();

        if (! $user) {
            return back()->withErrors(['nim' => 'NIM tidak ditemukan. Pastikan Anda sudah terdaftar, atau hubungi admin UPA-TIK.'])->onlyInput('nim');
        }

        Auth::login($user);
        return redirect()->route('mahasiswa.dashboard');
    }

    public function showDosen()
    {
        return view('identify.dosen');
    }

    public function identifyDosen(Request $request): RedirectResponse
    {
        $validated = $request->validate(['nip' => ['required', 'string']]);
        $user = User::where('role', 'dosen')->where('nip', $validated['nip'])->first();

        if (! $user) {
            return back()->withErrors(['nip' => 'NIP tidak ditemukan. Pastikan Anda sudah terdaftar, atau hubungi admin UPA-TIK.'])->onlyInput('nip');
        }

        Auth::login($user);
        return redirect()->route('mahasiswa.dashboard');
    }

    public function showPengunjung()
    {
        return view('identify.pengunjung');
    }

    public function identifyPengunjung(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => 'tamu-'.Str::uuid()->toString().'@pengunjung.local',
            'password' => Str::random(32),
            'role' => 'pengunjung',
        ]);

        Auth::login($user);
        return redirect()->route('mahasiswa.dashboard');
    }
}
