<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi | UPA-TIK Antrian UNIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top, #0c1830 0%, #040914 65%); }
        .glass { background: rgba(15,27,51,.55); backdrop-filter: blur(14px); border: 1px solid rgba(56,189,248,.12); }
        input, select, textarea {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        input::placeholder { color: #94a3b8 !important; }
        input:focus { outline: none; box-shadow: 0 0 0 2px rgba(34, 211, 238, 0.5); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center text-slate-100 px-4">
    <div class="glass rounded-2xl p-8 w-full max-w-md shadow-[0_0_35px_rgba(34,211,238,.1)]">
        <div class="text-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo UNIMA" class="w-16 h-16 mx-auto mb-3 object-contain">
            <h1 class="text-xl font-bold">Lupa Kata Sandi</h1>
            <p class="text-sm text-slate-400 mt-1">Masukkan email Anda dan buat kata sandi baru</p>
        </div>

        @if($errors->any())
        <div class="bg-rose-500/10 border border-rose-400/30 text-rose-300 text-sm rounded-lg px-4 py-3 mb-5">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs text-slate-400">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full mt-1 bg-navy-900/60 border border-cyan-400/20 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-400/60">
            </div>
            <div>
                <label class="text-xs text-slate-400">Kata Sandi Baru</label>
                <input type="password" name="password" required minlength="6"
                    class="w-full mt-1 bg-navy-900/60 border border-cyan-400/20 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-400/60">
            </div>
            <div>
                <label class="text-xs text-slate-400">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" required minlength="6"
                    class="w-full mt-1 bg-navy-900/60 border border-cyan-400/20 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-cyan-400/60">
            </div>
            <button class="w-full bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold rounded-lg py-2.5 text-sm transition shadow-[0_0_20px_rgba(34,211,238,.3)]">
                Ubah Kata Sandi
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 mt-6">
            Ingat kata sandi Anda? <a href="{{ route('login') }}" class="text-cyan-400 hover:underline">Kembali ke halaman masuk</a>
        </p>
    </div>
</body>
</html>
