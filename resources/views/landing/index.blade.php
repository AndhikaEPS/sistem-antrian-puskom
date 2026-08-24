<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrian PUSKOM UNIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top, #0c1830 0%, #040914 65%); }
        .glass { background: rgba(15,27,51,.55); backdrop-filter: blur(14px); border: 1px solid rgba(56,189,248,.12); }
        .glow-text { text-shadow: 0 0 30px rgba(34,211,238,.5); }
    </style>
</head>
<body class="min-h-screen text-slate-100 font-sans">

    <nav class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-700 flex items-center justify-center font-bold">P</div>
            <span class="font-semibold">PUSKOM <span class="text-cyan-400">UNIMA</span></span>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('login') }}" class="text-sm px-4 py-2 rounded-lg border border-cyan-400/30 hover:bg-cyan-400/10 transition">Masuk</a>
            <a href="{{ route('register') }}" class="text-sm px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-medium transition">Daftar</a>
        </div>
    </nav>

    <section class="max-w-5xl mx-auto px-6 pt-16 pb-20 text-center">
        <span class="inline-block text-xs tracking-widest text-cyan-300 border border-cyan-400/30 rounded-full px-4 py-1.5 mb-6">SISTEM ANTRIAN DIGITAL</span>
        <h1 class="text-4xl md:text-5xl font-bold leading-tight glow-text">Layanan PUSKOM UNIMA<br>Lebih Cepat, Mudah, dan Teratur.</h1>
        <p class="mt-6 text-slate-300 max-w-2xl mx-auto">Ambil nomor antrian secara digital dan pantau posisi antrian Anda tanpa harus menunggu terlalu lama di ruang pelayanan.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold shadow-[0_0_25px_rgba(34,211,238,.35)] transition">Ambil Nomor Antrian</a>
            <a href="#layanan" class="px-6 py-3 rounded-xl border border-cyan-400/30 hover:bg-cyan-400/10 transition">Lihat Layanan</a>
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

    <section id="layanan" class="max-w-6xl mx-auto px-6 pb-24">
        <h2 class="text-2xl font-bold mb-2 text-center">Jenis Layanan</h2>
        <p class="text-slate-400 text-center mb-10">Pilih layanan sesuai kebutuhan Anda saat mengambil nomor antrian.</p>
        <div class="grid md:grid-cols-3 gap-5">
            @foreach(\App\Models\Service::active()->orderBy('sort_order')->get() as $service)
            <div class="glass rounded-2xl p-6 hover:shadow-[0_0_25px_rgba(34,211,238,.15)] transition">
                <span class="text-xs font-mono text-cyan-400 border border-cyan-400/30 rounded px-2 py-0.5">{{ $service->service_code }}</span>
                <h3 class="font-semibold mt-3">{{ $service->service_name }}</h3>
                <p class="text-sm text-slate-400 mt-2">{{ $service->description }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-6 pb-24">
        <h2 class="text-2xl font-bold mb-8 text-center">Cara Menggunakan Sistem</h2>
        <div class="grid md:grid-cols-4 gap-4">
            @foreach(['Login / Daftar' => 'Masuk menggunakan akun mahasiswa Anda.', 'Pilih Layanan' => 'Tentukan jenis layanan yang dibutuhkan.', 'Ambil Nomor' => 'Sistem membuatkan nomor antrian otomatis.', 'Tunggu Panggilan' => 'Pantau posisi antrian secara real-time.'] as $title => $desc)
            <div class="glass rounded-2xl p-5">
                <h4 class="font-semibold text-cyan-300 mb-2">{{ $title }}</h4>
                <p class="text-sm text-slate-400">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <footer class="text-center text-xs text-slate-500 pb-10">
        &copy; {{ date('Y') }} Pusat Komputer (PUSKOM) Universitas Negeri Manado.
    </footer>
</body>
</html>
