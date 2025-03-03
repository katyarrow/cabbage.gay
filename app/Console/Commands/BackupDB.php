<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-d-b';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backs up the database to the storage folder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
    }
}
