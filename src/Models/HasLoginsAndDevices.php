<?php

namespace Lab404\AuthChecker\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasLoginsAndDevices
{
    /**
     * @param   void
     * @return  HasMany
     */
    public function logins()
    {
        return $this->hasMany(Login::class)->where('type', Login::TYPE_LOGIN);
    }

    /**
     * @param   void
     * @return  HasMany
     */
    public function attempts()
    {
        return $this->hasMany(Login::class)->where('type', Login::TYPE_ATTEMPT);
    }

    /**
     * @param   void
     * @return  HasMany
     */
    public function lockouts()
    {
        return $this->hasMany(Login::class)->where('type', Login::TYPE_LOCKOUT);
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
     * @return  bool
     */
    public function hasDevices()
    {
        return $this->devices && $this->devices->isNotEmpty();
    }
}
