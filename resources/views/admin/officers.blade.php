@extends('layouts.app')
@section('title', 'Manajemen Petugas')

@section('content')
<h1 class="text-2xl font-bold mb-6">Manajemen Petugas</h1>

<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-navy-900/60 text-slate-400 text-xs uppercase">
            <tr><th class="text-left px-5 py-3">Nama</th><th class="text-left px-5 py-3">Layanan Ditangani</th><th class="text-left px-5 py-3">No. Petugas</th><th class="text-left px-5 py-3">Status</th><th class="text-left px-5 py-3">Aksi</th></tr>
        </thead>
        <tbody class="divide-y divide-cyan-400/10">
            @foreach($officers as $officer)
            <tr class="hover:bg-cyan-400/5">
                <td class="px-5 py-3">{{ $officer->user->name }}</td>
                <td class="px-5 py-3 text-slate-400">{{ $officer->currentService->service_name ?? 'Semua Layanan' }}</td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('admin.officers.update', $officer) }}" class="flex items-center gap-2">
                        @csrf @method('PUT')
                        <input name="counter_number" type="number" value="{{ $officer->counter_number }}" class="w-16 bg-navy-900/60 border border-cyan-400/20 rounded-lg px-2 py-1 text-sm">
                </td>
                <td class="px-5 py-3">
                        <select name="status" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-2 py-1 text-sm">
                            <option value="available" {{ $officer->status=='available'?'selected':'' }}>Tersedia</option>
                            <option value="busy" {{ $officer->status=='busy'?'selected':'' }}>Sibuk</option>
                            <option value="offline" {{ $officer->status=='offline'?'selected':'' }}>Offline</option>
                        </select>
                </td>
                <td class="px-5 py-3">
                        <button class="text-xs px-3 py-1.5 rounded-md bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold transition">Simpan</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $officers->links() }}</div>
<p class="text-xs text-slate-500 mt-4">Untuk menambah petugas baru, gunakan menu Manajemen User dan pilih role "Petugas".</p>
@endsection
