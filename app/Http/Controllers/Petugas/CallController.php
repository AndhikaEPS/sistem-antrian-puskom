<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Officer;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    public function dashboard()
    {
        $officer = Officer::with('currentService')->where('user_id', Auth::id())->firstOrFail();

        $currentQueue = Queue::with(['service', 'user'])
            ->where('officer_id', $officer->id)
            ->today()
            ->whereIn('status', ['CALLED', 'SERVING'])
            ->latest('called_at')
            ->first();

        $waitingList = Queue::with(['service', 'user'])
            ->today()
            ->waiting()
            ->when($officer->current_service_id, fn ($q) => $q->where('service_id', $officer->current_service_id))
            ->limit(10)
            ->get();

        $services = Service::active()->orderBy('sort_order')->get();

        $statsToday = [
            'served' => Queue::where('officer_id', $officer->id)->today()->where('status', 'COMPLETED')->count(),
            'skipped' => Queue::where('officer_id', $officer->id)->today()->where('status', 'SKIPPED')->count(),
            'waiting_total' => Queue::today()->waiting()->count(),
        ];

        return view('petugas.dashboard', compact('officer', 'currentQueue', 'waitingList', 'services', 'statsToday'));
    }

    public function setService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['nullable', 'exists:services,id'],
            'status' => ['required', 'in:available,busy,offline'],
        ]);

        $officer = Officer::where('user_id', Auth::id())->firstOrFail();
        $officer->update([
            'current_service_id' => $validated['service_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Pengaturan loket diperbarui.');
    }

    /**
     * ALGORITMA: "Panggil Berikutnya" — mengambil antrian WAITING paling awal
     * (FIFO, queue_time ASC) untuk jenis layanan yang ditangani petugas ini.
     * Dibungkus transaction + lockForUpdate agar dua petugas tidak memanggil
     * nomor yang sama secara bersamaan (mencegah race condition).
     */
    public function callNext(): RedirectResponse
    {
        $officer = Officer::where('user_id', Auth::id())->firstOrFail();

        $stillActive = Queue::where('officer_id', $officer->id)->whereIn('status', ['CALLED', 'SERVING'])->exists();
        if ($stillActive) {
            return back()->with('error', 'Selesaikan atau lewati antrian yang sedang berjalan terlebih dahulu.');
        }

        $next = \Illuminate\Support\Facades\DB::transaction(function () use ($officer) {
            $query = Queue::where('status', 'WAITING')->today()->orderBy('queue_time')->lockForUpdate();
            if ($officer->current_service_id) {
                $query->where('service_id', $officer->current_service_id);
            }
            $queue = $query->first();

            if (! $queue) {
                return null;
            }

            $queue->update([
                'status' => 'CALLED',
                'officer_id' => $officer->id,
                'counter_number' => $officer->counter_number,
                'called_at' => now(),
            ]);
            $queue->logStatus('CALLED', Auth::id(), 'Dipanggil oleh petugas loket '.$officer->counter_number);

            return $queue;
        });

        if (! $next) {
            return back()->with('warning', 'Tidak ada antrian yang menunggu untuk layanan ini.');
        }

        $officer->update(['status' => 'busy']);

        return back()->with('success', "Nomor {$next->queue_number} berhasil dipanggil.");
    }

    public function recall(Queue $queue): RedirectResponse
    {
        $this->authorizeOfficerQueue($queue);
        $queue->update(['called_at' => now()]);
        $queue->logStatus('CALLED', Auth::id(), 'Dipanggil ulang');

        return back()->with('success', "Nomor {$queue->queue_number} dipanggil ulang.");
    }

    public function startServing(Queue $queue): RedirectResponse
    {
        $this->authorizeOfficerQueue($queue);
        $queue->update(['status' => 'SERVING', 'started_at' => now()]);
        $queue->logStatus('SERVING', Auth::id(), 'Mulai dilayani');

        return back()->with('success', "Mulai melayani {$queue->queue_number}.");
    }

    public function complete(Queue $queue): RedirectResponse
    {
        $this->authorizeOfficerQueue($queue);
        $queue->update(['status' => 'COMPLETED', 'completed_at' => now()]);
        $queue->logStatus('COMPLETED', Auth::id(), 'Pelayanan selesai');

        Officer::where('id', $queue->officer_id)->update(['status' => 'available']);

        return back()->with('success', "Pelayanan {$queue->queue_number} selesai.");
    }

    public function skip(Queue $queue): RedirectResponse
    {
        $this->authorizeOfficerQueue($queue);
        $queue->update(['status' => 'SKIPPED']);
        $queue->logStatus('SKIPPED', Auth::id(), 'Mahasiswa tidak hadir saat dipanggil');

        Officer::where('id', $queue->officer_id)->update(['status' => 'available']);

        return back()->with('warning', "Nomor {$queue->queue_number} dilewati.");
    }

    private function authorizeOfficerQueue(Queue $queue): void
    {
        $officer = Officer::where('user_id', Auth::id())->firstOrFail();
        abort_unless($queue->officer_id === $officer->id, 403, 'Antrian ini tidak sedang ditangani oleh Anda.');
    }
}
