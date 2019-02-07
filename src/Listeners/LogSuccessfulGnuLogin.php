<?php

namespace EvansKim\GnuMigration\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulGnuLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $event->user->mb_login_ip = request()->ip();
        $event->user->mb_today_login = date("Y-m-d H:i:s");
        $event->user->save();
    }
}
