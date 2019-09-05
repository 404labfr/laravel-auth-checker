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
    public function logins(): HasMany
    {
        $model = config('auth-checker.models.login') ?? Login::class;

        return $this->hasMany($model);
    }

    public function auths(): HasMany
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_LOGIN);

        return $relation;
    }

    public function fails(): HasMany
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_FAILED);

        return $relation;
    }

    public function lockouts(): HasMany
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_LOCKOUT);

        return $relation;
    }

    public function devices(): HasMany
    {
        $model = config('auth-checker.models.device') ?? Device::class;

        return $this->hasMany($model);
    }

    public function hasDevices(): bool
    {
        return $this->devices()->get()->isNotEmpty();
    }
}
