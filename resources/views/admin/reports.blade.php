@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<h1 class="text-2xl font-bold mb-6">Laporan Pelayanan</h1>

<form method="GET" class="glass rounded-2xl p-6 mb-6 grid md:grid-cols-6 gap-3 items-end">
    <div>
        <label class="text-xs text-slate-400 block mb-1">Dari Tanggal</label>
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm w-full">
    </div>
    <div>
        <label class="text-xs text-slate-400 block mb-1">Sampai Tanggal</label>
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm w-full">
    </div>
    <div>
        <label class="text-xs text-slate-400 block mb-1">Layanan</label>
        <select name="service_id" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm w-full">
            <option value="">Semua</option>
            @foreach($services as $s)
            <option value="{{ $s->id }}" {{ request('service_id')==$s->id?'selected':'' }}>{{ $s->service_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs text-slate-400 block mb-1">Petugas</label>
        <select name="officer_id" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm w-full">
            <option value="">Semua</option>
            @foreach($officers as $o)
            <option value="{{ $o->id }}" {{ request('officer_id')==$o->id?'selected':'' }}>{{ $o->user->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs text-slate-400 block mb-1">Status</label>
        <select name="status" class="bg-navy-900/60 border border-cyan-400/20 rounded-lg px-3 py-2 text-sm w-full">
            <option value="">Semua</option>
            @foreach(['WAITING','CALLED','SERVING','COMPLETED','SKIPPED','CANCELLED'] as $st)
            <option value="{{ $st }}" {{ request('status')==$st?'selected':'' }}>{{ $st }}</option>
            @endforeach
        </select>
    </div>
    <button class="text-sm px-4 py-2 rounded-lg border border-cyan-400/30 hover:bg-cyan-400/10 transition">Terapkan Filter</button>
</form>

<div class="flex gap-3 mb-6">
    <a href="{{ route('admin.reports.export-pdf', request()->query()) }}" class="text-sm px-4 py-2 rounded-lg bg-rose-500/80 hover:bg-rose-500 transition">Export PDF</a>
    <a href="{{ route('admin.reports.export-excel', request()->query()) }}" class="text-sm px-4 py-2 rounded-lg bg-emerald-500/80 hover:bg-emerald-500 transition">Export Excel</a>
    <button onclick="window.print()" class="text-sm px-4 py-2 rounded-lg border border-cyan-400/30 hover:bg-cyan-400/10 transition">Print</button>
</div>

<div class="glass rounded-2xl overflow-x-auto">
    <table class="w-full text-sm whitespace-nowrap">
        <thead class="bg-navy-900/60 text-slate-400 text-xs uppercase">
            <tr>
                <th class="text-left px-4 py-3">Tanggal</th><th class="text-left px-4 py-3">Nomor</th><th class="text-left px-4 py-3">Mahasiswa</th>
                <th class="text-left px-4 py-3">NIM</th><th class="text-left px-4 py-3">Layanan</th><th class="text-left px-4 py-3">Petugas</th>
                <th class="text-left px-4 py-3">Waktu Ambil</th><th class="text-left px-4 py-3">Dipanggil</th><th class="text-left px-4 py-3">Mulai</th>
                <th class="text-left px-4 py-3">Selesai</th><th class="text-left px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-cyan-400/10">
            @forelse($queues as $q)
            <tr class="hover:bg-cyan-400/5">
                <td class="px-4 py-3">{{ $q->queue_date->format('d/m/Y') }}</td>
                <td class="px-4 py-3 font-mono text-cyan-300">{{ $q->queue_number }}</td>
                <td class="px-4 py-3">{{ $q->user->name }}</td>
                <td class="px-4 py-3 text-slate-400">{{ $q->user->nim }}</td>
                <td class="px-4 py-3">{{ $q->service->service_name }}</td>
                <td class="px-4 py-3">{{ $q->officer?->user?->name ?? '-' }}</td>
                <td class="px-4 py-3 text-slate-400">{{ $q->queue_time }}</td>
                <td class="px-4 py-3 text-slate-400">{{ $q->called_at?->format('H:i') ?? '-' }}</td>
                <td class="px-4 py-3 text-slate-400">{{ $q->started_at?->format('H:i') ?? '-' }}</td>
                <td class="px-4 py-3 text-slate-400">{{ $q->completed_at?->format('H:i') ?? '-' }}</td>
                <td class="px-4 py-3">{{ $q->status }}</td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center py-8 text-slate-500">Tidak ada data untuk filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $queues->links() }}</div>
@endsection
