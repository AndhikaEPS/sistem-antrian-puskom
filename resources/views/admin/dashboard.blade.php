@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-2xl font-bold mb-8">Dashboard Admin</h1>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    <div class="glass rounded-2xl p-5"><p class="text-xs text-slate-400">Total Antrian Hari Ini</p><p class="text-3xl font-bold text-cyan-300 mt-2">{{ $totals['total'] }}</p></div>
    <div class="glass rounded-2xl p-5"><p class="text-xs text-slate-400">Selesai</p><p class="text-3xl font-bold text-emerald-300 mt-2">{{ $totals['completed'] }}</p></div>
    <div class="glass rounded-2xl p-5"><p class="text-xs text-slate-400">Menunggu</p><p class="text-3xl font-bold text-amber-300 mt-2">{{ $totals['waiting'] }}</p></div>
    <div class="glass rounded-2xl p-5"><p class="text-xs text-slate-400">Dibatalkan / Dilewati</p><p class="text-3xl font-bold text-rose-300 mt-2">{{ $totals['cancelled'] }}</p></div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="glass rounded-2xl p-6">
        <h3 class="font-semibold mb-4">Jumlah Antrian 7 Hari Terakhir</h3>
        <canvas id="chartDaily" height="180"></canvas>
    </div>
    <div class="glass rounded-2xl p-6">
        <h3 class="font-semibold mb-4">Antrian per Jenis Layanan (Bulan Ini)</h3>
        <canvas id="chartService" height="180"></canvas>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="glass rounded-2xl p-6">
        <h3 class="font-semibold mb-4">Jam Paling Ramai</h3>
        <canvas id="chartHours" height="180"></canvas>
    </div>
    <div class="glass rounded-2xl p-6">
        <h3 class="font-semibold mb-4">Pelayanan per Petugas (Hari Ini)</h3>
        <canvas id="chartOfficer" height="180"></canvas>
    </div>
</div>

<div class="glass rounded-2xl p-6 mb-10">
    <h3 class="font-semibold mb-4">Rata-rata Waktu Pelayanan per Layanan (Moving Average)</h3>
    <table class="w-full text-sm">
        <thead class="text-slate-400 text-xs uppercase"><tr><th class="text-left py-2">Layanan</th><th class="text-left py-2">Rata-rata (menit)</th></tr></thead>
        <tbody class="divide-y divide-cyan-400/10">
            @foreach($avgWaitBySrv as $row)
            <tr><td class="py-2">{{ $row['service'] }}</td><td class="py-2 text-cyan-300 font-semibold">{{ $row['avg_minutes'] }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
const chartColors = { cyan: '#22d3ee', blue: '#0284c7', grid: 'rgba(148,163,184,.1)', text: '#94a3b8' };
Chart.defaults.color = chartColors.text;
Chart.defaults.borderColor = chartColors.grid;

new Chart(document.getElementById('chartDaily'), {
    type: 'line',
    data: {
        labels: {!! json_encode($last7Days->pluck('queue_date')) !!},
        datasets: [{ label: 'Jumlah Antrian', data: {!! json_encode($last7Days->pluck('total')) !!}, borderColor: chartColors.cyan, backgroundColor: 'rgba(34,211,238,.15)', fill: true, tension: .3 }]
    },
    options: { plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('chartService'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($perService->pluck('service_name')) !!},
        datasets: [{ label: 'Jumlah', data: {!! json_encode($perService->pluck('total')) !!}, backgroundColor: chartColors.blue, borderRadius: 6 }]
    },
    options: { plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('chartHours'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($busiestHours->pluck('hour')->map(fn($h) => $h.':00')) !!},
        datasets: [{ label: 'Jumlah Antrian', data: {!! json_encode($busiestHours->pluck('total')) !!}, backgroundColor: chartColors.cyan, borderRadius: 6 }]
    },
    options: { plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('chartOfficer'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($perOfficer->pluck('name')) !!},
        datasets: [{ data: {!! json_encode($perOfficer->pluck('total')) !!}, backgroundColor: ['#22d3ee', '#0284c7', '#38bdf8', '#0369a1', '#7dd3fc'] }]
    }
});
</script>
@endpush
