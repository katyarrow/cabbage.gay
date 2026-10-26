<?php

namespace App\Console\Commands;

use App\Models\Rota;
use Illuminate\Console\Command;

class DeleteExpiredRotas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-rotas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes rotas where the destroy_at date is less than now.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Rota::where('destroy_at', '<', now())->delete();
    }
}
