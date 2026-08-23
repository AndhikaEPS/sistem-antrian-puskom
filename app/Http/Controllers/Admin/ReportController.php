<?php

namespace App\Http\Controllers\Admin;

use App\Exports\QueueReportExport;
use App\Http\Controllers\Controller;
use App\Models\Officer;
use App\Models\Queue;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $queues = $this->filteredQuery($request)->paginate(20)->withQueryString();
        $services = Service::orderBy('service_name')->get();
        $officers = Officer::with('user')->get();

        return view('admin.reports', compact('queues', 'services', 'officers'));
    }

    public function exportPdf(Request $request)
    {
        $queues = $this->filteredQuery($request)->get();
        $pdf = Pdf::loadView('admin.reports_pdf', compact('queues'))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-antrian-puskom-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $queues = $this->filteredQuery($request)->get();

        return Excel::download(new QueueReportExport($queues), 'laporan-antrian-puskom-'.now()->format('Ymd-His').'.xlsx');
    }

    private function filteredQuery(Request $request)
    {
        return Queue::with(['user', 'service', 'officer.user'])
            ->when($request->from_date, fn ($q) => $q->whereDate('queue_date', '>=', $request->from_date))
            ->when($request->to_date, fn ($q) => $q->whereDate('queue_date', '<=', $request->to_date))
            ->when($request->service_id, fn ($q) => $q->where('service_id', $request->service_id))
            ->when($request->officer_id, fn ($q) => $q->where('officer_id', $request->officer_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('queue_date')
            ->latest('queue_time');
    }
}
