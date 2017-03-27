<?php

namespace Lab404\AuthChecker\Events;

use Lab404\AuthChecker\Models\Device;

class DeviceCreated
{
    /** @var Device */
    public $device;

    /**
     * LoginCreated constructor.
     *
     * @param   Device $device
     */
    public function __construct(Device $device)
    {
        $this->device = $device;
    }
}
