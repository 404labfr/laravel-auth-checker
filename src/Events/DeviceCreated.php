<?php

namespace Lab404\AuthChecker\Events;

use Lab404\AuthChecker\Models\Device;
use Illuminate\Queue\SerializesModels;

class DeviceCreated
{
    use SerializesModels;

    /** @var Device $device */
    public $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }
}
