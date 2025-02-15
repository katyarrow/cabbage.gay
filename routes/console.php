<?php

use App\Console\Commands\DeleteExpiredMeetings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command(DeleteExpiredMeetings::class)->hourly();
