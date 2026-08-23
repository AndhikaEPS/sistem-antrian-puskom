@extends('layouts.app')
@section('title', 'Daftar Layanan')

@section('content')
<h1 class="text-2xl font-bold mb-1">Daftar Layanan PUSKOM</h1>
<p class="text-slate-400 text-sm mb-8">Pilih layanan sesuai kebutuhan Anda.</p>

<div class="grid md:grid-cols-2 gap-5">
    @foreach($services as $row)
    @php $service = $row['service']; @endphp
    <div class="glass rounded-2xl p-6">
        <div class="flex justify-between items-start">
            <div>
                <span class="text-xs font-mono text-cyan-400 border border-cyan-400/30 rounded px-2 py-0.5">{{ $service->service_code }}</span>
                <h3 class="font-semibold mt-3 text-lg">{{ $service->service_name }}</h3>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400">Sedang Dilayani</p>
                <p class="text-cyan-300 font-bold">{{ $row['currently_serving'] ?? '-' }}</p>
            </div>
        </div>
        <p class="text-sm text-slate-400 mt-2">{{ $service->description }}</p>
        <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
            <div class="bg-navy-900/50 rounded-lg px-3 py-2">
                <p class="text-xs text-slate-500">Menunggu</p>
                <p class="font-semibold">{{ $row['waiting_count'] }} orang</p>
            </div>
            <div class="bg-navy-900/50 rounded-lg px-3 py-2">
                <p class="text-xs text-slate-500">Rata-rata layanan</p>
                <p class="font-semibold">{{ $row['avg_duration'] }} menit</p>
            </div>
        </div>
        <form method="POST" action="{{ route('mahasiswa.queue.store', $service) }}" class="mt-4">
            @csrf
            <button class="w-full text-center text-sm px-4 py-2.5 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold transition">Ambil Nomor Antrian</button>
        </form>
    </div>
    @endforeach
</div>
@endsection
