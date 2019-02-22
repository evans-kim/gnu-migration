<?php
namespace EvansKim\GnuMigration;

use EvansKim\GnuMigration\Listeners\LogSuccessfulGnuLogin;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;


class GnuMigrationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeGnuBoard::class
            ]);
        }
        Auth::provider('gnu', function ($app, array $config) {
            return new GnuUserProvider();
        });

        $this->loadRoutesFrom(__DIR__ . "/routes.php");

        Event::listen(\Illuminate\Auth\Events\Login::class, LogSuccessfulGnuLogin::class);

        $this->loadViewsFrom(__DIR__.'/resources/views', 'gnu');
        $this->publishes([
            __DIR__.'/migrations' => database_path('/migrations'),
            __DIR__.'/resources' => resource_path('/vendor/gnu'),
        ]);
    }
}