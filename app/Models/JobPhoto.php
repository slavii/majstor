<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class JobPhoto extends Model
{
    protected $fillable = ['job_id', 'path', 'category', 'caption'];

    public const CATEGORIES = [
        'before' => 'Преди',
        'after' => 'След',
        'progress' => 'В процес',
        'general' => 'Общи',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function url(): string
    {
        return Storage::url($this->path);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
