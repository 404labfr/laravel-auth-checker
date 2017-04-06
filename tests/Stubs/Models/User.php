<?php

namespace Lab404\Tests\Stubs\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lab404\AuthChecker\Interfaces\HasLoginsAndDevicesInterface;
use Lab404\AuthChecker\Models\HasLoginsAndDevices;

class User extends Authenticatable implements HasLoginsAndDevicesInterface
{
    use Notifiable, HasLoginsAndDevices;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
