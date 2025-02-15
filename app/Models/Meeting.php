<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'destroy_at' => 'date',
        ];
    }

    // RELATIONSHIPS
    public function meetingAttendees()
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    // SCOPES
    public function scopeIdentifier($query, $identifier)
    {
        return $query->where('identifier', $identifier);
    }
}
