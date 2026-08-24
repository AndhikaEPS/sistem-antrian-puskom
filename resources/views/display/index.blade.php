<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian | PUSKOM UNIMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top, #0c1830 0%, #040914 70%); }
        .glass { background: rgba(15,27,51,.55); backdrop-filter: blur(14px); border: 1px solid rgba(56,189,248,.15); }
        .pulse-glow { animation: pulseGlow 2s ease-in-out infinite; }
        @keyframes pulseGlow {
            0%, 100% { text-shadow: 0 0 25px rgba(34,211,238,.5); }
            50% { text-shadow: 0 0 45px rgba(34,211,238,.9); }
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col">

    <header class="text-center pt-8 pb-4">
        <h1 class="text-2xl md:text-3xl font-bold tracking-widest text-cyan-300">PUSKOM UNIMA</h1>
        <p class="text-slate-400 text-sm md:text-base tracking-wide">SISTEM ANTRIAN DIGITAL</p>
    </header>

    <div class="border-t border-cyan-400/20 max-w-5xl mx-auto w-full"></div>

    <main class="flex-1 flex flex-col items-center justify-center py-10">
        <p class="text-slate-400 tracking-widest text-sm mb-3">NOMOR DIPANGGIL</p>
        <div id="main-number" class="text-7xl md:text-9xl font-black text-cyan-300 pulse-glow mb-4">
            {{ $servingNow->first()->queue_number ?? '---' }}
        </div>
        <div id="main-counter" class="text-xl md:text-2xl text-slate-200 tracking-wide">
            LOKET {{ $servingNow->first()->counter_number ?? '-' }}
        </div>
    </main>

    <div class="border-t border-cyan-400/20 max-w-5xl mx-auto w-full"></div>

    <section class="py-8 max-w-4xl mx-auto w-full px-6">
        <p class="text-center text-slate-400 text-sm mb-4 tracking-widest">SEDANG / TELAH DIPANGGIL</p>
        <div id="serving-list" class="grid grid-cols-3 gap-4">
            @foreach($servingNow as $q)
            <div class="glass rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-cyan-200">{{ $q->queue_number }}</p>
                <p class="text-xs text-slate-400 mt-1">Loket {{ $q->counter_number }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <footer class="text-center text-sm text-slate-400 py-6 tracking-wide">
        Silakan menuju loket pelayanan
    </footer>

    <!-- Overlay wajib diklik agar browser mengizinkan pemutaran suara otomatis
         (kebijakan keamanan browser: audio tidak boleh berbunyi sendiri tanpa
         ada interaksi pengguna terlebih dahulu di halaman ini). -->
    <div id="audio-unlock-overlay" class="fixed inset-0 bg-black/80 flex flex-col items-center justify-center z-50 cursor-pointer">
        <div class="glass rounded-2xl p-8 text-center max-w-sm mx-4">
            <p class="text-4xl mb-4">🔊</p>
            <h2 class="text-lg font-bold text-cyan-300 mb-2">Aktifkan Suara Panggilan</h2>
            <p class="text-sm text-slate-400 mb-6">Klik tombol di bawah sekali saja agar suara pemanggilan nomor antrian bisa otomatis berbunyi di layar ini.</p>
            <button id="audio-unlock-btn" class="px-6 py-3 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-navy-950 font-semibold transition">
                Aktifkan Suara
            </button>
        </div>
    </div>

    <script>
        let lastCalledId = {{ $servingNow->first()->id ?? 'null' }};
        const pollUrl = "{{ route('display.poll') }}";
        let audioUnlocked = false;

        function speak(text) {
            if (!('speechSynthesis' in window) || !audioUnlocked) return;
            window.speechSynthesis.cancel();
            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = 'id-ID';
            utter.rate = 0.9;
            window.speechSynthesis.speak(utter);
        }

        document.getElementById('audio-unlock-btn').addEventListener('click', function () {
            audioUnlocked = true;
            const test = new SpeechSynthesisUtterance('Suara diaktifkan');
            test.lang = 'id-ID';
            test.volume = 1;
            window.speechSynthesis.speak(test);
            document.getElementById('audio-unlock-overlay').style.display = 'none';
        });

        async function pollDisplay() {
            try {
                const res = await fetch(pollUrl);
                if (!res.ok) return;
                const data = await res.json();
                const serving = data.serving;
                if (!serving.length) return;

                const top = serving[0];
                document.getElementById('main-number').textContent = top.queue_number;
                document.getElementById('main-counter').textContent = 'LOKET ' + (top.counter_number ?? '-');

                document.getElementById('serving-list').innerHTML = serving.map(q => `
                    <div class="glass rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-cyan-200">${q.queue_number}</p>
                        <p class="text-xs text-slate-400 mt-1">Loket ${q.counter_number ?? '-'}</p>
                    </div>
                `).join('');

                if (top.id !== lastCalledId) {
                    lastCalledId = top.id;
                    speak(`Nomor antrian ${top.queue_number.split('').join(' ')}, silakan menuju Loket ${top.counter_number ?? ''}.`);
                }
            } catch (e) {
                console.error('Gagal polling display', e);
            }
        }

        setInterval(pollDisplay, 4000);
    </script>
</body>
</html>
