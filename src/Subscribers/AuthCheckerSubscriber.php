<?php

namespace Lab404\AuthChecker\Subscribers;

use Illuminate\Auth\Events\Login;
use Illuminate\Events\Dispatcher;
use Lab404\AuthChecker\Services\AuthChecker;

class AuthCheckerSubscriber
{
    /**
     * @param   Dispatcher $events
     * @return  void
     */
    public function subscribe($events)
    {
        $events->listen(Login::class, [$this, 'onUserLogin']);
    }

    /**
     * @param   Login $event
     * @return  void
     */
    public function onUserLogin(Login $event)
    {
        /** @var AuthChecker $manager */
        $manager = app('authchecker');
        $manager->handleLogin($event->user);
    }
}
