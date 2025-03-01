<?php

use App\Console\Commands\DeleteExpiredMeetings;
use App\Console\Commands\RemoveExpiredCaptchas;

Schedule::command(DeleteExpiredMeetings::class)->hourly();
Schedule::command(RemoveExpiredCaptchas::class)->everyMinute();
