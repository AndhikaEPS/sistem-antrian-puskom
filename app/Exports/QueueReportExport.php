<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class QueueReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $queues) {}

    public function collection(): Collection
    {
        return $this->queues;
    }

    public function headings(): array
    {
        return [
            'Tanggal', 'Nomor Antrian', 'Nama Mahasiswa', 'NIM', 'Jenis Layanan',
            'Petugas', 'Waktu Ambil', 'Waktu Dipanggil', 'Waktu Mulai', 'Waktu Selesai',
            'Durasi Menunggu (menit)', 'Durasi Pelayanan (menit)', 'Status',
        ];
    }

    public function map($queue): array
    {
        $waitMinutes = $queue->called_at
            ? \Carbon\Carbon::parse($queue->queue_date->format('Y-m-d').' '.$queue->queue_time)->diffInMinutes($queue->called_at)
            : null;

        $serviceMinutes = ($queue->started_at && $queue->completed_at)
            ? $queue->started_at->diffInMinutes($queue->completed_at)
            : null;

        return [
            $queue->queue_date->format('Y-m-d'),
            $queue->queue_number,
            $queue->user->name,
            $queue->user->nim,
            $queue->service->service_name,
            $queue->officer?->user?->name ?? '-',
            $queue->queue_time,
            $queue->called_at?->format('H:i:s') ?? '-',
            $queue->started_at?->format('H:i:s') ?? '-',
            $queue->completed_at?->format('H:i:s') ?? '-',
            $waitMinutes ?? '-',
            $serviceMinutes ?? '-',
            $queue->status,
        ];
    }
}
