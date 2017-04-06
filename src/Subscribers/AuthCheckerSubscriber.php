<?php

namespace Lab404\AuthChecker\Subscribers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
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
        $events->listen(Failed::class, [$this, 'onUserLoginFailed']);
        $events->listen(Lockout::class, [$this, 'onUserLoginLockout']);
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

    /**
     * @param   Failed $event
     * @return  void
     */
    public function onUserLoginFailed(Failed $event)
    {
        $user = $event->user;

        if (!is_null($user)) {
            /** @var AuthChecker $manager */
            $manager = app('authchecker');
            $manager->handleFailed($user);
        }
    }

    /**
     * @param   Lockout $event
     * @return  void
     */
    public function onUserLoginLockout(Lockout $event)
    {
        $payload = $event->request->all();

        if (!empty($payload)) {
            /** @var AuthChecker $manager */
            $manager = app('authchecker');
            $manager->handleLockout($payload);
        }
    }
}
