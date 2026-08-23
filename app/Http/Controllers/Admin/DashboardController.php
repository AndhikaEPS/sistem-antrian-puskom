<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $totals = [
            'total' => Queue::whereDate('queue_date', $today)->count(),
            'completed' => Queue::whereDate('queue_date', $today)->where('status', 'COMPLETED')->count(),
            'waiting' => Queue::whereDate('queue_date', $today)->where('status', 'WAITING')->count(),
            'cancelled' => Queue::whereDate('queue_date', $today)->whereIn('status', ['CANCELLED', 'SKIPPED'])->count(),
        ];

        // Grafik: jumlah antrian 7 hari terakhir
        $last7Days = Queue::selectRaw('queue_date, COUNT(*) as total')
            ->where('queue_date', '>=', now()->subDays(6)->toDateString())
            ->groupBy('queue_date')
            ->orderBy('queue_date')
            ->get();

        // Grafik: jumlah antrian per jenis layanan (bulan berjalan)
        $perService = Queue::join('services', 'services.id', '=', 'queues.service_id')
            ->selectRaw('services.service_name, COUNT(*) as total')
            ->whereMonth('queue_date', now()->month)
            ->groupBy('services.service_name')
            ->orderByDesc('total')
            ->get();

        // Jam paling ramai (agregasi berbasis jam dari queue_time)
        $busiestHours = Queue::selectRaw('HOUR(queue_time) as hour, COUNT(*) as total')
            ->whereMonth('queue_date', now()->month)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Pelayanan per petugas hari ini
        $perOfficer = Queue::join('officers', 'officers.id', '=', 'queues.officer_id')
            ->join('users', 'users.id', '=', 'officers.user_id')
            ->selectRaw('users.name, COUNT(*) as total')
            ->where('queues.status', 'COMPLETED')
            ->whereDate('queue_date', $today)
            ->groupBy('users.name')
            ->orderByDesc('total')
            ->get();

        $avgWaitBySrv = Service::active()->get()->map(fn ($s) => [
            'service' => $s->service_name,
            'avg_minutes' => $s->averageServiceDuration(),
        ]);

        return view('admin.dashboard', compact(
            'totals', 'last7Days', 'perService', 'busiestHours', 'perOfficer', 'avgWaitBySrv'
        ));
    }
}
