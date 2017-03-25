<?php

namespace Lab404\AuthChecker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    /** @var array */
    protected $casts = [
        'is_locked' => 'boolean',
        'is_desktop' => 'boolean',
        'is_phone' => 'boolean',
    ];

    /** @var array */
    protected $fillable = [
        'platform',
        'platform_version',
        'browser',
        'browser_version',
        'is_desktop',
        'is_phone',
        'language',
        'is_locked',
    ];

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
     * @return  BelongsTo
     */
    public function user()
    {
        $model = config('auth.providers.users.model');

        return $this->belongsTo($model);
    }
}
