<?php

namespace App\Http\Controllers;

use App\Models\Queue;

class DisplayController extends Controller
{
    public function index()
    {
        $servingNow = Queue::with('service')
            ->today()
            ->whereIn('status', ['CALLED', 'SERVING'])
            ->latest('called_at')
            ->limit(3)
            ->get();

        return view('display.index', compact('servingNow'));
    }

    /**
     * Endpoint AJAX polling untuk layar display, dipanggil tiap 3-5 detik.
     * Mengembalikan nomor yang baru saja dipanggil agar frontend dapat
     * memicu Web Speech API (browser text-to-speech) saat ada nomor baru.
     */
    public function poll()
    {
        $servingNow = Queue::with('service')
            ->today()
            ->whereIn('status', ['CALLED', 'SERVING'])
            ->latest('called_at')
            ->limit(3)
            ->get()
            ->map(fn ($q) => [
                'id' => $q->id,
                'queue_number' => $q->queue_number,
                'service_name' => $q->service->service_name,
                'counter_number' => $q->counter_number,
                'called_at' => $q->called_at?->timestamp,
            ]);

        return response()->json(['serving' => $servingNow]);
    }
}
