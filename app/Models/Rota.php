<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'destroy_at' => 'date',
            'updated_at' => 'datetime',
        ];
    }

    // SCOPES
    public function scopeIdentifier($query, $identifier)
    {
        return $query->where('identifier', $identifier);
    }
}
