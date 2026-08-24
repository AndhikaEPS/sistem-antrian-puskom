@extends('layouts.app')
@section('title', 'Dashboard Petugas')

@section('content')
<div class="flex flex-wrap justify-between items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold">Dashboard Petugas — {{ $officer->user->name }}</h1>
        <p class="text-slate-400 text-sm">{{ $officer->currentService->service_name ?? 'Semua Layanan' }}</p>
    </div>
    <form method="POST" action="{{ route('petugas.set-service') }}" class="flex gap-2 items-end flex-wrap">
        @csrf
        <div>
            <label class="text-xs text-slate-400 block mb-1">Layanan Ditangani</label>
            <select name="service_id" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Layanan</option>
                @foreach($services as $s)
                <option value="{{ $s->id }}" {{ $officer->current_service_id == $s->id ? 'selected' : '' }}>{{ $s->service_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-400 block mb-1">Status</label>
            <select name="status" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm">
                <option value="available" {{ $officer->status == 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="offline" {{ $officer->status == 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
        </div>
        <button class="text-sm px-4 py-2 rounded-lg border border-cyan-400/30 hover:bg-cyan-400/10 transition">Simpan</button>
    </form>
</div>

<div class="grid lg:grid-cols-3 gap-6 mb-8">
    <div class="glass rounded-2xl p-5 text-center"><p class="text-xs text-slate-400">Dilayani Hari Ini</p><p class="text-2xl font-bold text-cyan-300 mt-1">{{ $statsToday['served'] }}</p></div>
    <div class="glass rounded-2xl p-5 text-center"><p class="text-xs text-slate-400">Dilewati</p><p class="text-2xl font-bold text-rose-300 mt-1">{{ $statsToday['skipped'] }}</p></div>
    <div class="glass rounded-2xl p-5 text-center"><p class="text-xs text-slate-400">Total Menunggu (Semua Layanan)</p><p class="text-2xl font-bold mt-1">{{ $statsToday['waiting_total'] }}</p></div>
</div>

<div class="glass rounded-3xl p-8 text-center mb-8 glow-border">
    <p class="text-xs uppercase tracking-widest text-slate-400 mb-2">Sedang Dilayani</p>
    @if($currentQueue)
        <p class="text-6xl font-extrabold text-cyan-300 glow-text mb-2">{{ $currentQueue->queue_number }}</p>
        <p class="text-sm text-slate-400 mb-1">{{ $currentQueue->service->service_name }}</p>
        <p class="text-xs text-slate-500 mb-6">{{ $currentQueue->user->name }} &middot; {{ $currentQueue->user->nim }}</p>

        <div class="flex flex-wrap justify-center gap-3">
            <form method="POST" action="{{ route('petugas.recall', $currentQueue) }}">
                @csrf
                <button class="px-5 py-2.5 rounded-lg border border-cyan-400/30 hover:bg-cyan-400/10 text-sm transition">Panggil Ulang</button>
            </form>
            @if($currentQueue->status === 'CALLED')
            <form method="POST" action="{{ route('petugas.start', $currentQueue) }}">
                @csrf
                <button class="px-5 py-2.5 rounded-lg bg-blue-500 hover:bg-blue-400 text-navy-950 font-semibold text-sm transition">Mulai Melayani</button>
            </form>
            @endif
            <form method="POST" action="{{ route('petugas.complete', $currentQueue) }}">
                @csrf
                <button class="px-5 py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-navy-950 font-semibold text-sm transition">Selesaikan</button>
            </form>
            <form method="POST" action="{{ route('petugas.skip', $currentQueue) }}" onsubmit="return confirm('Lewati antrian ini?');">
                @csrf
                <button class="px-5 py-2.5 rounded-lg border border-rose-400/30 text-rose-300 hover:bg-rose-400/10 text-sm transition">Lewati</button>
            </form>
        </div>
    @else
        <p class="text-3xl font-bold text-slate-600 mb-6">- Tidak Ada -</p>
        <form method="POST" action="{{ route('petugas.call-next') }}">
            @csrf
            <button class="px-8 py-3 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-bold transition shadow-[0_0_20px_rgba(34,211,238,.3)]">Panggil Berikutnya</button>
        </form>
    @endif
</div>

<h2 class="text-lg font-semibold mb-4">Antrian Menunggu (FIFO)</h2>
<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-navy-900/60 text-slate-400 text-xs uppercase">
            <tr><th class="text-left px-5 py-3">Nomor</th><th class="text-left px-5 py-3">Layanan</th><th class="text-left px-5 py-3">Waktu Ambil</th><th class="text-left px-5 py-3">Nama</th></tr>
        </thead>
        <tbody class="divide-y divide-cyan-400/10">
            @forelse($waitingList as $q)
            <tr class="hover:bg-cyan-400/5">
                <td class="px-5 py-3 font-mono text-cyan-300">{{ $q->queue_number }}</td>
                <td class="px-5 py-3">{{ $q->service->service_name }}</td>
                <td class="px-5 py-3 text-slate-400">{{ $q->queue_time }}</td>
                <td class="px-5 py-3">{{ $q->user->name }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-8 text-slate-500">Tidak ada antrian menunggu.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh ringan agar daftar tunggu tetap terkini di layar petugas.
    setTimeout(() => window.location.reload(), 15000);
</script>
@endpush
