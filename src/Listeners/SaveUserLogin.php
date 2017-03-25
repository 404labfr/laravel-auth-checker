<?php

namespace Lab404\AuthChecker\Listeners;

use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Database\Eloquent\Model;
use Lab404\AuthChecker\Events\LoginCreated;
use Lab404\AuthChecker\Models\Login as LoginModel;

class SaveUserLogin
{
    /**
     * @param   Login
     * @return  void
     */
    public function handle(LoginEvent $event)
    {
        /** @var Model $user */
        $user  = $event->user;
        $ip    = request()->ip();

        /** @var LoginModel $login */
        $login = new LoginModel(['user_id' => $user->getKey(), 'ip_address' => $ip]);
        $user->logins()->save($login);

        event(new LoginCreated($user, $login));
    }
}
