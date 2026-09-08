<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrian UPA-TIK UNIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top, #0c1830 0%, #040914 65%); }
        .glass { background: rgba(15,27,51,.55); backdrop-filter: blur(14px); border: 1px solid rgba(56,189,248,.12); }
        .glow-text { text-shadow: 0 0 30px rgba(34,211,238,.5); }
        .hero-img-glow { box-shadow: 0 0 60px rgba(34,211,238,.15); }
    </style>
</head>
<body class="min-h-screen text-slate-100 font-sans">

    <nav class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" alt="Logo UNIMA" class="w-9 h-9 object-contain">
            <span class="font-semibold">UPA-TIK <span class="text-cyan-400">UNIMA</span></span>
        </div>
        <a href="{{ route('login') }}" class="text-xs px-4 py-2 rounded-lg border border-cyan-400/30 hover:bg-cyan-400/10 transition text-slate-300">Login Admin / Petugas</a>
    </nav>

    <section class="max-w-7xl mx-auto px-6 pt-12 pb-10">
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div class="text-center md:text-left">
                <span class="inline-block text-xs tracking-widest text-cyan-300 border border-cyan-400/30 rounded-full px-4 py-1.5 mb-6">SISTEM ANTRIAN DIGITAL</span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight glow-text">Layanan UPA-TIK UNIMA<br>Lebih Cepat, Mudah, dan Teratur.</h1>
                <p class="mt-6 text-slate-300 max-w-xl mx-auto md:mx-0">Pilih salah satu di bawah ini untuk langsung mengambil nomor antrian.</p>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-cyan-500/10 rounded-3xl blur-2xl"></div>
                <img src="{{ asset('hero-office.jpg') }}" alt="Ilustrasi kantor layanan UPA-TIK" class="relative rounded-2xl w-full hero-img-glow border border-cyan-400/10">
            </div>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-6 pb-16">
        <div class="grid md:grid-cols-3 gap-5">
            <a href="{{ route('identify.mahasiswa') }}" class="glass rounded-2xl p-8 text-center hover:shadow-[0_0_30px_rgba(34,211,238,.2)] transition group">
                <div class="w-14 h-14 mx-auto rounded-xl bg-cyan-500/15 flex items-center justify-center text-2xl mb-4 group-hover:bg-cyan-500/25 transition">🎓</div>
                <h3 class="font-semibold text-lg mb-1">Mahasiswa</h3>
                <p class="text-xs text-slate-400">Masuk dengan NIM Anda</p>
            </a>
            <a href="{{ route('identify.dosen') }}" class="glass rounded-2xl p-8 text-center hover:shadow-[0_0_30px_rgba(34,211,238,.2)] transition group">
                <div class="w-14 h-14 mx-auto rounded-xl bg-cyan-500/15 flex items-center justify-center text-2xl mb-4 group-hover:bg-cyan-500/25 transition">👨‍🏫</div>
                <h3 class="font-semibold text-lg mb-1">Dosen</h3>
                <p class="text-xs text-slate-400">Masuk dengan NIP Anda</p>
            </a>
            <a href="{{ route('identify.pengunjung') }}" class="glass rounded-2xl p-8 text-center hover:shadow-[0_0_30px_rgba(34,211,238,.2)] transition group">
                <div class="w-14 h-14 mx-auto rounded-xl bg-cyan-500/15 flex items-center justify-center text-2xl mb-4 group-hover:bg-cyan-500/25 transition">🧑‍💼</div>
                <h3 class="font-semibold text-lg mb-1">Pengunjung</h3>
                <p class="text-xs text-slate-400">Tanpa perlu akun</p>
            </a>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-4 mb-20">
        @php
            $stats = [
                ['label' => 'Jenis Layanan', 'value' => \App\Models\Service::active()->count()],
                ['label' => 'Antrian Hari Ini', 'value' => \App\Models\Queue::whereDate('queue_date', now())->count()],
                ['label' => 'Jam Pelayanan', 'value' => '08:00-16:00'],
                ['label' => 'Petugas Aktif', 'value' => \App\Models\Officer::where('status', '!=', 'offline')->count()],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="glass rounded-2xl p-5 text-center">
            <p class="text-2xl font-bold text-cyan-300">{{ $s['value'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </section>

    <footer class="text-center text-xs text-slate-500 pb-10">
        &copy; {{ date('Y') }} UPA-TIK (Unit Penunjang Akademik - Teknologi Informasi dan Komunikasi) Universitas Negeri Manado.
    </footer>
</body>
</html>
