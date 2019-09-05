<?php

namespace Lab404\AuthChecker\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function logins(): HasMany;

    public function auths(): HasMany;

    public function fails(): HasMany;

    public function lockouts(): HasMany;

    public function devices(): HasMany;

    public function hasDevices(): bool;

    /**
     * @return mixed
     */
    public function getKey();
}
