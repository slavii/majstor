<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientCommunication extends Model
{
    protected $fillable = ['client_id', 'user_id', 'job_id', 'type', 'direction', 'summary'];

    public const TYPES = [
        'call' => 'Обаждане',
        'viber' => 'Viber',
        'sms' => 'SMS',
        'email' => 'Имейл',
        'in_person' => 'На живо',
        'other' => 'Друго',
    ];

    public const DIRECTIONS = [
        'inbound' => 'Входящо',
        'outbound' => 'Изходящо',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function directionLabel(): string
    {
        return self::DIRECTIONS[$this->direction] ?? $this->direction;
    }

    public function typeIcon(): string
    {
        return match ($this->type) {
            'call' => 'phone',
            'viber' => 'chat-bubble-left-right',
            'sms' => 'device-phone-mobile',
            'email' => 'envelope',
            'in_person' => 'user',
            default => 'chat-bubble-left',
        };
    }
}
