<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CachedListing extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'has_delivery' => 'boolean',
            'price' => 'integer',
            'posted_at' => 'datetime',
        ];
    }
}
