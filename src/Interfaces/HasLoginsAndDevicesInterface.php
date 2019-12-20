<?php

namespace Lab404\AuthChecker\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @package Lab404\AuthChecker\Interfaces
 * @property \Illuminate\Support\Collection $logins
 * @property \Illuminate\Support\Collection $auths
 * @property \Illuminate\Support\Collection $fails
 * @property \Illuminate\Support\Collection $lockouts
 * @property \Illuminate\Support\Collection $devices
 */
interface HasLoginsAndDevicesInterface
{
    public function logins(): MorphMany;

    public function auths(): MorphMany;

    public function fails(): MorphMany;

    public function lockouts(): MorphMany;

    public function devices(): MorphMany;

    public function hasDevices(): bool;

    /**
     * @return mixed
     */
    public function getKey();
}
