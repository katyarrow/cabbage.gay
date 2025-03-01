<?php

namespace App\Console\Commands;

use App\Models\PowCaptcha;
use Illuminate\Console\Command;

class RemoveExpiredCaptchas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-expired-captchas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete expired captchas.
        $deleteBefore = now()->subSeconds(config('captcha.max_captcha_lifetime'));
        PowCaptcha::where('created_at', '<', $deleteBefore)->delete();

        // Delete captchas where it has been verified but not used within a reasonable amount of time
        // In this case max response execution time + a little extra.
        PowCaptcha::query()
            ->whereNotNull('solved_at')
            ->where('solved_at', '<', now()->subSeconds(45))
            ->delete();
    }
}
