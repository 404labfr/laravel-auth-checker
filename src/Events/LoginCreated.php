<?php

namespace Lab404\AuthChecker\Events;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;
use Lab404\AuthChecker\Models\Login;

class LoginCreated
{
    /** @var Model */
    public $user;

    /** @var Login */
    public $login;

    /** @var Agent */
    public $agent;

    /**
     * LoginCreated constructor.
     *
     * @param   Model $user
     * @param   Login $login
     */
    public function __construct(Model $user, Login $login, $agent = null)
    {
        $this->user = $user;
        $this->login = $login;
        $this->agent = is_null($agent) ? app('agent') : $agent;
    }
}
