<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowCaptcha extends Model
{
    protected $casts = [
        'solved_at' => 'datetime',
    ];
}
