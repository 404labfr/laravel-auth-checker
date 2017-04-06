<?php

namespace Lab404\AuthChecker\Interfaces;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Interface HasLoginsAndDevices
 *
 * @package Lab404\AuthChecker\Interfaces
 * @property \Illuminate\Support\Collection $logins
 * @property \Illuminate\Support\Collection $auths
 * @property \Illuminate\Support\Collection $fails
 * @property \Illuminate\Support\Collection $lockouts
 * @property \Illuminate\Support\Collection $devices
 */
interface HasLoginsAndDevicesInterface
{
    /**
     * @return  HasMany
     */
    public function logins();

    /**
     * @return  HasMany
     */
    public function auths();

    /**
     * @return  HasMany
     */
    public function fails();

    /**
     * @return  HasMany
     */
    public function lockouts();

    /**
     * @return  HasMany
     */
    public function devices();

    /**
     * @return  bool
     */
    public function hasDevices();

    /**
     * @return mixed
     */
    public function getKey();
}
