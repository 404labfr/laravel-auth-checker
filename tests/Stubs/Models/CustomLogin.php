<?php

namespace Lab404\Tests\Stubs\Models;

use Lab404\AuthChecker\Models\Login;

class CustomLogin extends Login
{
    /** @var string $table */
    protected $table = 'logins';
}
