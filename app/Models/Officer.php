<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Officer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'counter_number', 'status', 'current_service_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentService(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'current_service_id');
    }

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Jumlah antrian yang sedang ditangani (belum selesai) hari ini.
     * Dipakai untuk strategi load balancing least-loaded-first.
     */
    public function activeLoadToday(): int
    {
        return $this->queues()
            ->whereDate('queue_date', now()->toDateString())
            ->whereIn('status', ['CALLED', 'SERVING'])
            ->count();
    }
}
