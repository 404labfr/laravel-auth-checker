<?php

namespace Lab404\AuthChecker\Events;

use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;

class FailedAuth
{
    /** @var Login */
    public $login;

    /** @var Device */
    public $device;

    /**
     * FailedAuth constructor.
     *
     * @param   Login $login
     * @param   Device $device
     */
    public function __construct(Login $login, Device $device)
    {
        $this->login = $login;
        $this->device = $device;
    }
}
