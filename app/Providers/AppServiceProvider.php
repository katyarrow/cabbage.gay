<?php

namespace App\Providers;

use App\Extensions\SimpleDatabaseSessionHandler;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ConnectionInterface $connection): void
    {
        Session::extend('database-simple', function (Application $app) use ($connection) {
            $table   = \Config::get('session.table');
            $minutes = \Config::get('session.lifetime');
            return new SimpleDatabaseSessionHandler($connection, $table, $minutes);
        });
    }
}
