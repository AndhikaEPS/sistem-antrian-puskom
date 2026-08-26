<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UPA-TIK Antrian') | UNIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 950: '#040914', 900: '#070f1f', 800: '#0c1830', 700: '#132144' },
                        ocean: { 600: '#0369a1', 500: '#0284c7', 400: '#38bdf8' },
                        cyanglow: '#22d3ee',
                    },
                    boxShadow: {
                        glow: '0 0 25px rgba(34, 211, 238, 0.25)',
                    },
                }
            }
        }
    </script>
    <style>
        body { background: radial-gradient(circle at top, #0c1830 0%, #040914 65%); }
        .glass {
            background: rgba(15, 27, 51, 0.55);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(56, 189, 248, 0.12);
        }
        .glow-border { border: 1px solid rgba(34, 211, 238, 0.35); }
        .glow-text { text-shadow: 0 0 18px rgba(34, 211, 238, 0.45); }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background: #0284c7; border-radius: 8px; }
        .transition-smooth { transition: all .25s ease; }

        input, select, textarea {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        input::placeholder, textarea::placeholder {
            color: #94a3b8 !important;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(34, 211, 238, 0.5);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen text-slate-100 font-sans antialiased">

    @auth
    <nav class="glass sticky top-0 z-50 border-b border-cyan-400/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo UNIMA" class="w-9 h-9 object-contain">
                    <span class="font-semibold tracking-wide text-cyan-50">UPA-TIK <span class="text-cyan-400">UNIMA</span></span>
                </div>
                <div class="hidden md:flex items-center gap-6 text-sm text-slate-300">
                    @if(auth()->user()->isMahasiswa())
                        <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-cyan-300 transition-smooth">Dashboard</a>
                        <a href="{{ route('mahasiswa.services') }}" class="hover:text-cyan-300 transition-smooth">Layanan</a>
                        <a href="{{ route('mahasiswa.history') }}" class="hover:text-cyan-300 transition-smooth">Riwayat</a>
                    @elseif(auth()->user()->isPetugas())
                        <a href="{{ route('petugas.dashboard') }}" class="hover:text-cyan-300 transition-smooth">Pemanggilan Antrian</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-300 transition-smooth">Dashboard</a>
                        <a href="{{ route('admin.users.index') }}" class="hover:text-cyan-300 transition-smooth">Users</a>
                        <a href="{{ route('admin.services.index') }}" class="hover:text-cyan-300 transition-smooth">Layanan</a>
                        <a href="{{ route('admin.officers.index') }}" class="hover:text-cyan-300 transition-smooth">Petugas</a>
                        <a href="{{ route('admin.reports.index') }}" class="hover:text-cyan-300 transition-smooth">Laporan</a>
                    @endif
                    <a href="{{ route('display.index') }}" target="_blank" class="hover:text-cyan-300 transition-smooth">Display</a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-slate-400 hidden sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-xs px-3 py-1.5 rounded-md border border-cyan-400/30 hover:bg-cyan-400/10 transition-smooth">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="glass glow-border rounded-xl px-4 py-3 mb-6 text-sm text-emerald-300">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="glass rounded-xl px-4 py-3 mb-6 text-sm text-amber-300 border border-amber-400/20">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
            <div class="glass rounded-xl px-4 py-3 mb-6 text-sm text-rose-300 border border-rose-400/20">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
