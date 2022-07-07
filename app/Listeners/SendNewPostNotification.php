<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewPostNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendNewPostNotification
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
        $users = User::get();
        Notification::send($users, new NewPostNotification($event->user));
    }
}
