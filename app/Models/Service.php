<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_code', 'service_name', 'description',
        'estimated_duration', 'status', 'sort_order',
    ];

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Estimasi durasi layanan (dalam menit) menggunakan pendekatan
     * Simple Moving Average dari N transaksi COMPLETED terakhir.
     * Jika belum ada data historis yang cukup, fallback ke estimated_duration default.
     */
    public function averageServiceDuration(int $sampleSize = 10): float
    {
        $durations = $this->queues()
            ->where('status', 'COMPLETED')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->limit($sampleSize)
            ->get()
            ->map(fn ($q) => $q->started_at->diffInSeconds($q->completed_at) / 60);

        if ($durations->isEmpty()) {
            return (float) $this->estimated_duration;
        }

        return round($durations->avg(), 1);
    }
}
