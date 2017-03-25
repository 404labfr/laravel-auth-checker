<?php

namespace Lab404\AuthChecker\Events;

use Illuminate\Database\Eloquent\Model;
use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;

class DeviceCreated
{
    /** @var Model */
    public $user;

    /** @var Login */
    public $login;

    /** @var Device */
    public $device;

    /**
     * LoginCreated constructor.
     *
     * @param   Model $user
     * @param   Login $login
     */
    public function __construct(Model $user, Login $login, Device $device)
    {
        $this->user = $user;
        $this->login = $login;
        $this->device = $device;
    }

    /**
     * @param   void
     * @return  void
     */
    public function handle()
    {
        //
    }
}
