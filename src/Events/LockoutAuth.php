<?php

namespace Lab404\AuthChecker\Events;

use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;

class LockoutAuth
{
    /** @var Login $login */
    public $login;
    /** @var Device $device */
    public $device;

    public function __construct(Login $login, Device $device)
    {
        $this->login = $login;
        $this->device = $device;
    }
}
