<?php

namespace App\Models;

use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    /** @use HasFactory<JobFactory> */
    use HasFactory;

    protected $table = 'jobs_tracker';

    protected $fillable = [
        'user_id',
        'client_id',
        'title',
        'description',
        'status',
        'scheduled_date',
        'estimated_price',
        'actual_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'estimated_price' => 'decimal:2',
            'actual_price' => 'decimal:2',
        ];
    }

    public const STATUSES = [
        'new' => 'Нова',
        'scheduled' => 'Насрочена',
        'in_progress' => 'В процес',
        'completed' => 'Завършена',
        'cancelled' => 'Отказана',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(JobPhoto::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(JobComment::class)->latest();
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(JobStatusHistory::class)->latest();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'new' => 'blue',
            'scheduled' => 'yellow',
            'in_progress' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
