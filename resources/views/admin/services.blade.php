@extends('layouts.app')
@section('title', 'Manajemen Layanan')

@section('content')
<h1 class="text-2xl font-bold mb-6">Manajemen Jenis Layanan</h1>

<div class="glass rounded-2xl p-6 mb-8">
    <h3 class="font-semibold mb-4">Tambah Jenis Layanan</h3>
    <form method="POST" action="{{ route('admin.services.store') }}" class="grid md:grid-cols-4 gap-4">
        @csrf
        <input name="service_code" placeholder="Kode (mis. AK)" maxlength="5" required class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm uppercase">
        <input name="service_name" placeholder="Nama Layanan" required class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm md:col-span-2">
        <input name="estimated_duration" type="number" placeholder="Estimasi Durasi (menit)" required class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
        <textarea name="description" placeholder="Deskripsi" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm md:col-span-3"></textarea>
        <input name="sort_order" type="number" placeholder="Urutan Tampil" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
        <button class="md:col-span-4 bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold rounded-lg py-2.5 text-sm transition">Tambah Layanan</button>
    </form>
</div>

<div class="grid md:grid-cols-2 gap-5">
    @foreach($services as $service)
    <div class="glass rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-3">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <input name="service_code" value="{{ $service->service_code }}" maxlength="5" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm uppercase">
                <select name="status" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
                    <option value="active" {{ $service->status=='active'?'selected':'' }}>Aktif</option>
                    <option value="inactive" {{ $service->status=='inactive'?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <input name="service_name" value="{{ $service->service_name }}" class="w-full bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
            <textarea name="description" class="w-full bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">{{ $service->description }}</textarea>
            <div class="grid grid-cols-2 gap-3">
                <input name="estimated_duration" type="number" value="{{ $service->estimated_duration }}" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
                <input name="sort_order" type="number" value="{{ $service->sort_order }}" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex gap-2">
                <button class="flex-1 text-sm px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold transition">Simpan</button>
            </div>
        </form>
        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="mt-2" onsubmit="return confirm('Hapus/nonaktifkan layanan ini?');">
            @csrf @method('DELETE')
            <button class="w-full text-xs px-4 py-2 rounded-lg border border-rose-400/30 text-rose-300 hover:bg-rose-400/10 transition">Hapus Layanan</button>
        </form>
    </div>
    @endforeach
</div>
<div class="mt-6">{{ $services->links() }}</div>
@endsection
