<?php

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use Illuminate\Console\Command;

class GenerateAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-analytics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate analytics for the system.';

    /**
     * Execute the console command.
     */
    public function handle() {
        $service = new AnalyticsService();

        $service->generateTotalMeetingsData();
        $service->generateAverageResponsesPerMeetingData();
        $service->generateMaxResponsesOnMeetingData();
    }
}
