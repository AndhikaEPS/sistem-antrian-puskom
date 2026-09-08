<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Antrian - Mahasiswa | UPA-TIK UNIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top, #0c1830 0%, #040914 65%); }
        .glass { background: rgba(15,27,51,.55); backdrop-filter: blur(14px); border: 1px solid rgba(56,189,248,.12); }
        input { background-color: #ffffff !important; color: #0f172a !important; }
        input::placeholder { color: #94a3b8 !important; }
        input:focus { outline: none; box-shadow: 0 0 0 2px rgba(34, 211, 238, 0.5); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center text-slate-100 px-4">
    <div class="glass rounded-2xl p-8 w-full max-w-md shadow-[0_0_35px_rgba(34,211,238,.1)]">
        <div class="text-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo UNIMA" class="w-16 h-16 mx-auto mb-3 object-contain">
            <h1 class="text-xl font-bold">Ambil Nomor Antrian</h1>
            <p class="text-sm text-slate-400 mt-1">Masukkan NIM Anda untuk melanjutkan</p>
        </div>
        @if($errors->any())
        <div class="bg-rose-500/10 border border-rose-400/30 text-rose-300 text-sm rounded-lg px-4 py-3 mb-5">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('identify.mahasiswa.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs text-slate-400">NIM</label>
                <input type="text" name="nim" value="{{ old('nim') }}" required autofocus class="w-full mt-1 border border-cyan-400/20 rounded-lg px-4 py-2.5 text-sm">
            </div>
            <button class="w-full bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold rounded-lg py-2.5 text-sm transition shadow-[0_0_20px_rgba(34,211,238,.3)]">Lanjutkan</button>
        </form>
        <p class="text-center text-xs text-slate-400 mt-6"><a href="{{ route('landing') }}" class="text-cyan-400 hover:underline">&larr; Kembali ke beranda</a></p>
    </div>
</body>
</html>
