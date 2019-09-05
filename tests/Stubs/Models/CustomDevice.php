<?php

namespace Lab404\Tests\Stubs\Models;

use Lab404\AuthChecker\Models\Device;

class CustomDevice extends Device
{
    /** @var string $table */
    protected $table = 'devices';
}
