<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware otorisasi berbasis role.
 * Daftarkan sebagai alias 'role' di app/Http/Kernel.php lalu pakai di rute:
 *   Route::middleware(['auth', 'role:admin'])->group(...)
 *   Route::middleware(['auth', 'role:admin,petugas'])->group(...)
 *
 * Mahasiswa TIDAK akan pernah bisa mengakses rute yang di-guard 'role:admin'
 * atau 'role:petugas' meski tahu URL-nya, karena dicek di server setiap request.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403, 'Akun tidak aktif atau belum login.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
