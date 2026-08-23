@extends('layouts.app')
@section('title', 'Nomor Antrian Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass rounded-3xl p-8 text-center glow-border" id="queue-card">

        <p class="text-xs tracking-widest text-slate-400 uppercase">Nomor Antrian Anda</p>
        <p class="text-6xl font-extrabold text-cyan-300 glow-text my-4">{{ $queue->queue_number }}</p>

        <p class="text-sm text-slate-400">Layanan</p>
        <p class="font-semibold mb-6">{{ $queue->service->service_name }}</p>

        <div class="grid grid-cols-2 gap-4 text-left">
            <div class="bg-navy-900/50 rounded-xl p-4">
                <p class="text-xs text-slate-400">Sedang Dilayani</p>
                <p id="currently-serving" class="text-2xl font-bold text-cyan-300 mt-1">-</p>
            </div>
            <div class="bg-navy-900/50 rounded-xl p-4">
                <p class="text-xs text-slate-400">Orang di Depan Anda</p>
                <p id="people-ahead" class="text-2xl font-bold mt-1">-</p>
            </div>
            <div class="bg-navy-900/50 rounded-xl p-4">
                <p class="text-xs text-slate-400">Estimasi Waktu</p>
                <p id="estimated-time" class="text-2xl font-bold mt-1">± - menit</p>
            </div>
            <div class="bg-navy-900/50 rounded-xl p-4">
                <p class="text-xs text-slate-400">Status</p>
                <p id="queue-status" class="text-2xl font-bold mt-1 text-amber-300">{{ $queue->status }}</p>
            </div>
        </div>

        <div id="called-banner" class="hidden mt-6 bg-emerald-500/15 border border-emerald-400/40 rounded-xl p-5">
            <p class="text-emerald-300 font-bold text-lg">DIPANGGIL</p>
            <p class="text-sm text-slate-200 mt-1">Silakan menuju Loket <span id="counter-number" class="font-bold">-</span></p>
        </div>

        <div class="flex gap-3 mt-8">
            @if(in_array($queue->status, ['WAITING', 'CALLED']))
            <form method="POST" action="{{ route('mahasiswa.queue.cancel', $queue) }}" class="flex-1"
                onsubmit="return confirm('Yakin ingin membatalkan antrian ini?');">
                @csrf
                <button class="w-full text-sm px-4 py-2.5 rounded-lg border border-rose-400/30 text-rose-300 hover:bg-rose-400/10 transition">Batalkan Antrian</button>
            </form>
            @endif
            <a href="{{ route('mahasiswa.dashboard') }}" class="flex-1 text-center text-sm px-4 py-2.5 rounded-lg border border-cyan-400/30 hover:bg-cyan-400/10 transition">Kembali ke Dashboard</a>
        </div>
    </div>

    @if($queue->status === 'COMPLETED' && !$queue->rating)
    <div class="glass rounded-2xl p-6 mt-6">
        <h3 class="font-semibold mb-3">Beri Penilaian Layanan</h3>
        <form method="POST" action="{{ route('mahasiswa.queue.rate', $queue) }}" class="space-y-3">
            @csrf
            <div class="flex gap-2">
                @for($i = 1; $i <= 5; $i++)
                <label class="cursor-pointer">
                    <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" {{ $i == 5 ? 'checked' : '' }}>
                    <span class="text-2xl peer-checked:text-amber-300 text-slate-600">★</span>
                </label>
                @endfor
            </div>
            <textarea name="feedback" rows="2" placeholder="Kritik dan saran (opsional)" class="w-full bg-navy-900/60 border border-cyan-400/20 rounded-lg px-4 py-2.5 text-sm"></textarea>
            <button class="text-sm px-5 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold transition">Kirim Penilaian</button>
        </form>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Real-time (near real-time) update via AJAX polling setiap 4 detik.
const statusUrl = "{{ route('mahasiswa.queue.status', $queue) }}";
const statusLabelColor = {
    WAITING: 'text-amber-300', CALLED: 'text-emerald-300', SERVING: 'text-cyan-300',
    COMPLETED: 'text-slate-400', SKIPPED: 'text-rose-300', CANCELLED: 'text-rose-400',
};

async function pollStatus() {
    try {
        const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const data = await res.json();

        document.getElementById('currently-serving').textContent = data.currently_serving ?? '-';
        document.getElementById('people-ahead').textContent = data.people_ahead;
        document.getElementById('estimated-time').textContent = '± ' + data.estimated_minutes + ' menit';

        const statusEl = document.getElementById('queue-status');
        statusEl.textContent = data.status;
        statusEl.className = 'text-2xl font-bold mt-1 ' + (statusLabelColor[data.status] || '');

        const banner = document.getElementById('called-banner');
        if (data.status === 'CALLED' || data.status === 'SERVING') {
            banner.classList.remove('hidden');
            document.getElementById('counter-number').textContent = data.counter_number ?? '-';
        }

        if (['COMPLETED', 'CANCELLED', 'SKIPPED'].includes(data.status)) {
            clearInterval(pollInterval);
        }
    } catch (e) {
        console.error('Gagal memuat status antrian', e);
    }
}

pollStatus();
const pollInterval = setInterval(pollStatus, 4000);
</script>
@endpush
