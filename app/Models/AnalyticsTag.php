<?php

namespace App\Models;

use App\Enums\AnalyticsTagNames;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AnalyticsTag extends Model
{
    public $timestamps = false;

    public static function getByName(AnalyticsTagNames|string $name) {
        return static::byName($name)->first();
    }

    public function scopeByName(Builder $query, AnalyticsTagNames|string $name): Builder {
        if(gettype($name) !== 'string') {
            $name = $name->value;
        }
        return $query->where('name', $name);
    }
}
