<?php
namespace EvansKim\GnuMigration;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class GnuMigrationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Auth::provider('gnu', function ($app, array $config) {

            return new GnuUserProvider();
        });

    }
}