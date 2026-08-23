<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->search, fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('nim', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['nullable', 'string', 'max:20', 'unique:users,nim'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'role' => ['required', Rule::in(['mahasiswa', 'petugas', 'admin'])],
            'counter_number' => ['required_if:role,petugas', 'nullable', 'integer', 'min:1'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nim' => $validated['nim'] ?? null,
            'email' => $validated['email'],
            'password' => $validated['password'], // Disimpan apa adanya, tanpa hashing.
            'role' => $validated['role'],
        ]);

        if ($validated['role'] === 'petugas') {
            $user->officer()->create([
                'counter_number' => $validated['counter_number'],
                'status' => 'offline',
            ]);
        }

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'is_active' => ['boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Data pengguna diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->role === 'admin', 403, 'Akun admin tidak dapat dihapus dari sini.');
        $user->delete();

        return back()->with('success', 'Pengguna dihapus.');
    }
}
