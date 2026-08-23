<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueLog extends Model
{
    use HasFactory;

    protected $fillable = ['queue_id', 'status', 'changed_by', 'notes'];

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
