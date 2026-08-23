@extends('layouts.app')
@section('title', 'Riwayat Antrian')

@section('content')
<h1 class="text-2xl font-bold mb-6">Riwayat Antrian Saya</h1>

<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-navy-900/60 text-slate-400 text-xs uppercase">
            <tr>
                <th class="text-left px-5 py-3">Tanggal</th>
                <th class="text-left px-5 py-3">Nomor</th>
                <th class="text-left px-5 py-3">Layanan</th>
                <th class="text-left px-5 py-3">Status</th>
                <th class="text-left px-5 py-3">Rating</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-cyan-400/10">
            @forelse($queues as $queue)
            <tr class="hover:bg-cyan-400/5">
                <td class="px-5 py-3 text-slate-300">{{ $queue->queue_date->format('d/m/Y') }}</td>
                <td class="px-5 py-3 font-mono text-cyan-300">{{ $queue->queue_number }}</td>
                <td class="px-5 py-3">{{ $queue->service->service_name }}</td>
                <td class="px-5 py-3">
                    <span class="text-xs px-2 py-1 rounded-full
                        @if($queue->status == 'COMPLETED') bg-emerald-500/15 text-emerald-300
                        @elseif($queue->status == 'CANCELLED' || $queue->status == 'SKIPPED') bg-rose-500/15 text-rose-300
                        @else bg-amber-500/15 text-amber-300 @endif">
                        {{ $queue->status }}
                    </span>
                </td>
                <td class="px-5 py-3">{{ $queue->rating ? str_repeat('★', $queue->rating) : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-8 text-slate-500">Belum ada riwayat antrian.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $queues->links() }}</div>
@endsection
