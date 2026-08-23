<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    /**
     * Dashboard mahasiswa: nomor antrian saat ini per layanan aktif milik
     * mahasiswa (jika ada), jumlah orang menunggu, estimasi waktu tunggu.
     */
    public function dashboard()
    {
        $user = Auth::user();

        $activeQueue = Queue::with('service')
            ->where('user_id', $user->id)
            ->today()
            ->whereIn('status', ['WAITING', 'CALLED', 'SERVING'])
            ->latest('id')
            ->first();

        $services = Service::active()->orderBy('sort_order')->get();

        return view('mahasiswa.dashboard', compact('user', 'activeQueue', 'services'));
    }

    public function services()
    {
        $services = Service::active()->orderBy('sort_order')->get()->map(function (Service $service) {
            $waitingCount = Queue::where('service_id', $service->id)->today()->waiting()->count();
            $currentlyServing = Queue::where('service_id', $service->id)->today()
                ->where('status', 'SERVING')->latest('started_at')->first();

            return [
                'service' => $service,
                'waiting_count' => $waitingCount,
                'currently_serving' => $currentlyServing?->queue_number,
                'avg_duration' => $service->averageServiceDuration(),
            ];
        });

        return view('mahasiswa.services', compact('services'));
    }

    /**
     * Ambil nomor antrian baru. Mahasiswa hanya boleh punya SATU antrian
     * aktif (WAITING/CALLED/SERVING) pada satu waktu untuk menghindari
     * penyalahgunaan sistem (ambil banyak nomor sekaligus).
     */
    public function store(Request $request, Service $service): RedirectResponse
    {
        $user = Auth::user();

        $existing = Queue::where('user_id', $user->id)
            ->today()
            ->whereIn('status', ['WAITING', 'CALLED', 'SERVING'])
            ->first();

        if ($existing) {
            return redirect()->route('mahasiswa.queue.show', $existing)
                ->with('warning', 'Anda masih memiliki antrian aktif. Selesaikan atau batalkan terlebih dahulu.');
        }

        $queue = Queue::generateForService($service, $user);

        return redirect()->route('mahasiswa.queue.show', $queue)
            ->with('success', 'Nomor antrian berhasil dibuat: '.$queue->queue_number);
    }

    public function show(Queue $queue)
    {
        $this->authorizeOwner($queue);
        $queue->load('service');

        return view('mahasiswa.queue_number', compact('queue'));
    }

    /**
     * Endpoint AJAX polling untuk update real-time (dipanggil tiap beberapa
     * detik dari JS di halaman nomor antrian & dashboard).
     */
    public function status(Queue $queue)
    {
        $this->authorizeOwner($queue);

        $currentlyServing = Queue::where('service_id', $queue->service_id)
            ->today()
            ->where('status', 'SERVING')
            ->latest('started_at')
            ->first();

        return response()->json([
            'status' => $queue->status,
            'queue_number' => $queue->queue_number,
            'currently_serving' => $currentlyServing?->queue_number,
            'people_ahead' => $queue->status === 'WAITING' ? $queue->positionInQueue() : 0,
            'estimated_minutes' => $queue->status === 'WAITING' ? $queue->estimatedWaitMinutes() : 0,
            'counter_number' => $queue->counter_number,
        ]);
    }

    public function cancel(Queue $queue): RedirectResponse
    {
        $this->authorizeOwner($queue);

        if (! in_array($queue->status, ['WAITING', 'CALLED'])) {
            return back()->with('error', 'Antrian ini tidak dapat dibatalkan.');
        }

        $queue->update(['status' => 'CANCELLED']);
        $queue->logStatus('CANCELLED', Auth::id(), 'Dibatalkan oleh mahasiswa');

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Antrian berhasil dibatalkan.');
    }

    public function rate(Request $request, Queue $queue): RedirectResponse
    {
        $this->authorizeOwner($queue);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($queue->status !== 'COMPLETED') {
            return back()->with('error', 'Rating hanya dapat diberikan setelah pelayanan selesai.');
        }

        $queue->update($validated);

        return back()->with('success', 'Terima kasih atas penilaian Anda.');
    }

    public function history()
    {
        $queues = Queue::with('service')
            ->where('user_id', Auth::id())
            ->latest('queue_date')
            ->latest('queue_time')
            ->paginate(15);

        return view('mahasiswa.history', compact('queues'));
    }

    private function authorizeOwner(Queue $queue): void
    {
        abort_unless($queue->user_id === Auth::id(), 403, 'Antrian ini bukan milik Anda.');
    }
}
