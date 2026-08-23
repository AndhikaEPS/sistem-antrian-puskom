<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_number', 'user_id', 'service_id', 'officer_id',
        'queue_date', 'queue_time', 'called_at', 'started_at', 'completed_at',
        'status', 'counter_number', 'rating', 'feedback',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(Officer::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(QueueLog::class)->orderByDesc('created_at');
    }

    /**
     * ALGORITMA: Pembuatan nomor antrian otomatis, atomic per layanan per hari.
     *
     * Menggunakan database transaction + row locking (SELECT ... FOR UPDATE)
     * pada baris terakhir milik service+tanggal yang sama, sehingga aman dari
     * race condition ketika dua mahasiswa mengambil nomor pada waktu bersamaan.
     * Nomor selalu reset ke 001 setiap hari & setiap jenis layanan (format
     * penomoran/padding dapat diatur lewat tabel settings oleh admin).
     */
    public static function generateForService(Service $service, User $user): self
    {
        return DB::transaction(function () use ($service, $user) {
            $today = now()->toDateString();

            $lastQueue = self::where('service_id', $service->id)
                ->whereDate('queue_date', $today)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $lastSequence = 0;
            if ($lastQueue) {
                $parts = explode('-', $lastQueue->queue_number);
                $lastSequence = (int) end($parts);
            }

            $nextSequence = $lastSequence + 1;
            $padding = (int) (Setting::get('queue_number_padding', 3));
            $queueNumber = $service->service_code.'-'.str_pad($nextSequence, $padding, '0', STR_PAD_LEFT);

            $queue = self::create([
                'queue_number' => $queueNumber,
                'user_id' => $user->id,
                'service_id' => $service->id,
                'queue_date' => $today,
                'queue_time' => now()->toTimeString(),
                'status' => 'WAITING',
            ]);

            $queue->logStatus('WAITING', $user->id, 'Nomor antrian diambil oleh mahasiswa');

            return $queue;
        });
    }

    /**
     * ALGORITMA FIFO: antrian aktif diurutkan berdasarkan waktu pengambilan
     * paling awal (queue_time ASC) — yang pertama datang, pertama dilayani.
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'WAITING')->orderBy('queue_time');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('queue_date', now()->toDateString());
    }

    /**
     * Posisi mahasiswa dalam antrian FIFO (berapa orang di depannya yang
     * masih WAITING untuk layanan yang sama, hari ini).
     */
    public function positionInQueue(): int
    {
        return self::where('service_id', $this->service_id)
            ->whereDate('queue_date', $this->queue_date)
            ->where('status', 'WAITING')
            ->where('queue_time', '<', $this->queue_time)
            ->count();
    }

    /**
     * Estimasi waktu tunggu (menit) = jumlah orang di depan x rata-rata
     * durasi pelayanan (Moving Average dari histori, fallback ke default).
     */
    public function estimatedWaitMinutes(): float
    {
        $peopleAhead = $this->positionInQueue();
        $avgDuration = $this->service->averageServiceDuration();

        return round($peopleAhead * $avgDuration, 1);
    }

    public function logStatus(string $status, ?int $changedBy = null, ?string $notes = null): void
    {
        $this->logs()->create([
            'status' => $status,
            'changed_by' => $changedBy,
            'notes' => $notes,
        ]);
    }
}
