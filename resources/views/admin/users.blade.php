@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Manajemen Pengguna</h1>
</div>

<div class="glass rounded-2xl p-6 mb-8">
    <h3 class="font-semibold mb-4">Tambah Pengguna Baru</h3>
    <form method="POST" action="{{ route('admin.users.store') }}" class="grid md:grid-cols-3 gap-4">
        @csrf
        <input name="name" placeholder="Nama Lengkap" required class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
        <input name="nim" placeholder="NIM (jika mahasiswa)" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
        <input name="email" type="email" placeholder="Email" required class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
        <input name="password" type="password" placeholder="Kata Sandi" required class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
        <select name="role" required class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
            <option value="mahasiswa">Mahasiswa</option>
            <option value="petugas">Petugas</option>
            <option value="admin">Admin</option>
        </select>
        <input name="counter_number" type="number" placeholder="No. Loket (jika petugas)" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
        <button class="md:col-span-3 bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold rounded-lg py-2.5 text-sm transition">Tambah Pengguna</button>
    </form>
</div>

<form method="GET" class="flex gap-3 mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email/NIM" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm flex-1">
    <select name="role" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
        <option value="">Semua Role</option>
        <option value="mahasiswa" {{ request('role')=='mahasiswa'?'selected':'' }}>Mahasiswa</option>
        <option value="petugas" {{ request('role')=='petugas'?'selected':'' }}>Petugas</option>
        <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
    </select>
    <button class="px-4 py-2 rounded-lg border border-cyan-400/30 hover:bg-cyan-400/10 text-sm transition">Filter</button>
</form>

<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-navy-900/60 text-slate-400 text-xs uppercase">
            <tr><th class="text-left px-5 py-3">Nama</th><th class="text-left px-5 py-3">Email / NIM</th><th class="text-left px-5 py-3">Role</th><th class="text-left px-5 py-3">Status</th><th class="text-left px-5 py-3">Aksi</th></tr>
        </thead>
        <tbody class="divide-y divide-cyan-400/10">
            @foreach($users as $user)
            <tr class="hover:bg-cyan-400/5">
                <td class="px-5 py-3">{{ $user->name }}</td>
                <td class="px-5 py-3 text-slate-400">{{ $user->email }} {{ $user->nim ? '/ '.$user->nim : '' }}</td>
                <td class="px-5 py-3 capitalize">{{ $user->role }}</td>
                <td class="px-5 py-3">
                    <span class="text-xs px-2 py-1 rounded-full {{ $user->is_active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-5 py-3 flex gap-2">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                        <button class="text-xs px-3 py-1.5 rounded-md border border-cyan-400/30 hover:bg-cyan-400/10 transition">
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    @if($user->role !== 'admin')
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini?');">
                        @csrf @method('DELETE')
                        <button class="text-xs px-3 py-1.5 rounded-md border border-rose-400/30 text-rose-300 hover:bg-rose-400/10 transition">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection
