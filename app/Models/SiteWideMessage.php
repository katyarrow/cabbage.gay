<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteWideMessage extends Model
{
    protected $casts = [
        'display_until' => 'datetime',
    ];

    public function scopeCurrentlyViewable(Builder $query) {
        return $query->where('display_until', '>', now());
    }

    public function scopeNotCurrentlyViewable(Builder $query) {
        return $query->where('display_until', '<=', now());
    }
}
