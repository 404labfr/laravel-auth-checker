<?php

namespace Lab404\AuthChecker\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class HasLoginsAndDevices
 *
 * @package Lab404\AuthChecker\Models
 */
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
    public function auths()
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_LOGIN);

        return $relation;
    }

    /**
     * @param   void
     * @return  HasMany
     */
    public function fails()
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_FAILED);

        return $relation;
    }

    /**
     * @param   void
     * @return  HasMany
     */
    public function lockouts()
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_LOCKOUT);

        return $relation;
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
        return $this->devices()->get()->isNotEmpty();
    }
}
