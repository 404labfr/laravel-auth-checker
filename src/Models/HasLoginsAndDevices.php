<?php

namespace Lab404\AuthChecker\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class HasLoginsAndDevices
 *
 * @package Lab404\AuthChecker\Models
 */
trait HasLoginsAndDevices
{
    public function logins(): MorphMany
    {
        $model = config('auth-checker.models.login') ?? Login::class;

        return $this->morphMany($model, 'user');
    }

    public function auths(): MorphMany
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_LOGIN);

        return $relation;
    }

    public function fails(): MorphMany
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_FAILED);

        return $relation;
    }

    public function lockouts(): MorphMany
    {
        $relation = $this->logins();
        $relation->where('type', Login::TYPE_LOCKOUT);

        return $relation;
    }

    public function devices(): MorphMany
    {
        $model = config('auth-checker.models.device') ?? Device::class;

        return $this->morphMany($model, 'user');
    }

    public function hasDevices(): bool
    {
        return $this
            ->devices()
            ->select('id')
            ->get()
            ->isNotEmpty();
    }
}
