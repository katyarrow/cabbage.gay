<?php

use App\Console\Commands\DeleteExpiredMeetings;
use App\Console\Commands\RemoveExpiredCaptchas;

Schedule::command(DeleteExpiredMeetings::class)->hourly();
Schedule::command(RemoveExpiredCaptchas::class)->everyMinute();

// Backups
Schedule::command('backup:clean')->daily()->at('11:00');
Schedule::command('backup:monitor')->daily()->at('11:00');
Schedule::command('backup:run')->everyFifteenMinutes();
