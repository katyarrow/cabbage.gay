<?php

namespace App\Console\Commands;

use App\Models\User;
use Hash;
use Illuminate\Console\Command;

class MakeAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make an admin user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->ask('Username: ');
        $password = $this->secret('Password: ');
        $user = new User;
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->save();
    }
}
