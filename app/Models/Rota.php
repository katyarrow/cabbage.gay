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
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Rota $rota) {
            $rota->generateAndSetNewVersion();
        });

        static::updating(function (Rota $rota) {
            $rota->generateAndSetNewVersion();
        });

    }

    // Does not save().
    public function generateAndSetNewVersion()
    {
        $this->version = str()->random(16);
    }

    // SCOPES
    public function scopeIdentifier($query, $identifier)
    {
        return $query->where('identifier', $identifier);
    }
}
