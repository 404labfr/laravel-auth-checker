<?php

namespace Lab404\AuthChecker\Subscribers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Lab404\AuthChecker\Interfaces\HasLoginsAndDevicesInterface;
use Lab404\AuthChecker\Services\AuthChecker;

class AuthCheckerSubscriber
{
    public function subscribe($events): void
    {
        $events->listen(Login::class, [$this, 'onUserLogin']);
        $events->listen(Failed::class, [$this, 'onUserLoginFailed']);
        $events->listen(Lockout::class, [$this, 'onUserLoginLockout']);
    }

    public function onUserLogin(Login $event): void
    {
        /** @var HasLoginsAndDevicesInterface $user */
        $user = $event->user;

        if ($user instanceof HasLoginsAndDevicesInterface) {
            /** @var AuthChecker $manager */
            $manager = app('authchecker');
            $manager->handleLogin($user);
        }
    }

    public function onUserLoginFailed(Failed $event): void
    {
        /** @var HasLoginsAndDevicesInterface $user */
        $user = $event->user;

        if (!is_null($user) && $user instanceof HasLoginsAndDevicesInterface) {
            /** @var AuthChecker $manager */
            $manager = app('authchecker');
            $manager->handleFailed($user);
        }
    }

    public function onUserLoginLockout(Lockout $event): void
    {
        $payload = $event->request->all();

        if (!empty($payload)) {
            /** @var AuthChecker $manager */
            $manager = app('authchecker');
            $manager->handleLockout($payload);
        }
    }
}
