<?php

namespace Lab404\AuthChecker\Events;

use Lab404\AuthChecker\Models\Login;

class LoginCreated
{
    /** @var Login $login */
    public $login;

    public function __construct(Login $login)
    {
        $this->login = $login;
    }
}
