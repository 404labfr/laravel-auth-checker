<?php

namespace Lab404\AuthChecker\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Jenssegers\Agent\Agent;

trait HasLoginsAndDevices
{
    /**
     * @param   void
     * @return  HasMany
     */
    public function logins()
    {
        return $this->hasMany(Login::class);
    }

    /**
     * @param   void
     * @return  HasMany
     */
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    /**
     * @param   void
     * @return  Carbon|null
     */
    public function getLastLoggedAtAttribute()
    {
        return $this->getLastLoginAttribute() ? $this->getLastLoginAttribute()->created_at : null;
    }

    /**
     * @param   void
     * @return  Login|null
     */
    public function getLastLoginAttribute()
    {
        return $this->hasLogins() ? $this->logins->first() : null;
    }

    /**
     * @param   void
     * @return  string|null
     */
    public function getLastIpAddressAttribute()
    {
        return $this->hasLogins() ? $this->logins->first()->ip_address : null;
    }

    /**
     * @param   void
     * @return  array|null
     */
    public function getIpAddressesAttribute()
    {
        return $this->hasLogins() ? $this->logins->unique('ip_address')->pluck('ip_address')->toArray() : [];
    }

    /**
     * @param   Agent $agent
     * @return  bool
     */
    public function hasDeviceFromAgent(Agent $agent)
    {
        if (! $this->hasDevices()) {
            return false;
        }

        return $this->devices->filter(function ($item) use ($agent) {
            return $item->platform == $agent->platform()
                    && $item->platform_version == $agent->version($item->platform)
                    && $item->browser == $agent->browser();
        })->count() >= 1;
    }

    /**
     * @param   void
     * @return  bool
     */
    public function hasLogins()
    {
        return $this->logins && $this->logins->isNotEmpty();
    }

    /**
     * @param   void
     * @return  bool
     */
    public function hasDevices()
    {
        return $this->devices && $this->devices->isNotEmpty();
    }
}
