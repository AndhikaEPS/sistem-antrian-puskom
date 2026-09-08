@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')

@section('content')
<h1 class="text-2xl font-bold mb-1">Selamat Datang, {{ $user->name }}</h1>
<p class="text-slate-400 text-sm mb-8">
    @if($user->isMahasiswa()) NIM {{ $user->nim }}
    @elseif($user->isDosen()) NIP {{ $user->nip }}
    @else Pengunjung
    @endif
    &middot; {{ now()->translatedFormat('l, d F Y') }}
</p>

@if($activeQueue)
<div class="glass rounded-2xl p-6 mb-8 glow-border">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs text-slate-400 uppercase tracking-wide">Antrian Aktif Anda</p>
            <p class="text-4xl font-bold text-cyan-300 glow-text mt-1">{{ $activeQueue->queue_number }}</p>
            <p class="text-sm text-slate-400 mt-1">{{ $activeQueue->service->service_name }}</p>
        </div>
        <a href="{{ route('mahasiswa.queue.show', $activeQueue) }}" class="px-5 py-2.5 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold text-sm transition">
            Lihat Detail Antrian
        </a>
    </div>
</div>
@endif

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    <div class="glass rounded-2xl p-5">
        <p class="text-xs text-slate-400">Nomor Antrian Saya</p>
        <p class="text-2xl font-bold mt-2 text-cyan-300">{{ $activeQueue->queue_number ?? '-' }}</p>
    </div>
    <div class="glass rounded-2xl p-5">
        <p class="text-xs text-slate-400">Status Pelayanan</p>
        <p class="text-2xl font-bold mt-2">{{ $activeQueue->status ?? 'Tidak Ada' }}</p>
    </div>
    <div class="glass rounded-2xl p-5">
        <p class="text-xs text-slate-400">Layanan Aktif</p>
        <p class="text-2xl font-bold mt-2 text-cyan-300">{{ $services->count() }}</p>
    </div>
    <div class="glass rounded-2xl p-5">
        <p class="text-xs text-slate-400">Riwayat Anda</p>
        <a href="{{ route('mahasiswa.history') }}" class="text-sm text-cyan-400 hover:underline mt-2 inline-block">Lihat riwayat &rarr;</a>
    </div>
</div>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Pilih Layanan untuk Ambil Antrian</h2>
    <a href="{{ route('mahasiswa.services') }}" class="text-sm text-cyan-400 hover:underline">Lihat semua &rarr;</a>
</div>

<div class="grid md:grid-cols-3 gap-5">
    @foreach($services->take(6) as $service)
    <div class="glass rounded-2xl p-6 hover:shadow-[0_0_25px_rgba(34,211,238,.15)] transition">
        <span class="text-xs font-mono text-cyan-400 border border-cyan-400/30 rounded px-2 py-0.5">{{ $service->service_code }}</span>
        <h3 class="font-semibold mt-3">{{ $service->service_name }}</h3>
        <p class="text-sm text-slate-400 mt-2 mb-4">{{ $service->description }}</p>
        @if($activeQueue)
            <button disabled class="w-full text-center text-xs px-4 py-2 rounded-lg bg-slate-700/40 text-slate-500 cursor-not-allowed">Anda punya antrian aktif</button>
        @else
            <form method="POST" action="{{ route('mahasiswa.queue.store', $service) }}">
                @csrf
                <button class="w-full text-center text-xs px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold transition">Ambil Nomor Antrian</button>
            </form>
        @endif
    </div>
    @endforeach
</div>
@endsection
