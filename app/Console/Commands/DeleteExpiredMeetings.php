<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use Illuminate\Console\Command;

class DeleteExpiredMeetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-meetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes meetings where the destroy_at date is less than now.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Meeting::where('destroy_at', '<', now())->delete();
    }
}
