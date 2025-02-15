<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAttendee extends Model
{
    public $timestamps = false;

    // RELATIONSHIPS
    public function meeting() {
        return $this->belongsTo(Meeting::class);
    }

    // SCOPES
    public function scopeIdentifier($query, $identifier)
    {
        return $query->where('identifier', $identifier);
    }
}
