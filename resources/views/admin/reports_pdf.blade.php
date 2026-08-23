<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        h2 { text-align: center; margin-bottom: 2px; }
        p.sub { text-align: center; color: #64748b; margin-top: 0; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #0f172a; color: #fff; }
        tr:nth-child(even) { background: #f1f5f9; }
    </style>
</head>
<body>
    <h2>Laporan Pelayanan PUSKOM Universitas Negeri Manado</h2>
    <p class="sub">Dicetak pada {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th><th>Nomor</th><th>Mahasiswa</th><th>NIM</th><th>Layanan</th>
                <th>Petugas</th><th>Ambil</th><th>Dipanggil</th><th>Mulai</th><th>Selesai</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($queues as $q)
            <tr>
                <td>{{ $q->queue_date->format('d/m/Y') }}</td>
                <td>{{ $q->queue_number }}</td>
                <td>{{ $q->user->name }}</td>
                <td>{{ $q->user->nim }}</td>
                <td>{{ $q->service->service_name }}</td>
                <td>{{ $q->officer?->user?->name ?? '-' }}</td>
                <td>{{ $q->queue_time }}</td>
                <td>{{ $q->called_at?->format('H:i:s') ?? '-' }}</td>
                <td>{{ $q->started_at?->format('H:i:s') ?? '-' }}</td>
                <td>{{ $q->completed_at?->format('H:i:s') ?? '-' }}</td>
                <td>{{ $q->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
