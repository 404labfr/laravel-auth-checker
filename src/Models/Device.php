<?php

namespace Lab404\AuthChecker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Device
 *
 * @package Lab404\AuthChecker\Models
 * @property int $id
 * @property \Lab404\AuthChecker\Models\Login $login
 * @property \Lab404\AuthChecker\Models\Login[] $logins
 * @property \Illuminate\Contracts\Auth\Authenticatable $user
 * @property int $user_id
 * @property string $platform
 * @property string $platform_version
 * @property string $browser
 * @property string $browser_version
 * @property bool $is_desktop
 * @property bool $is_mobile
 * @property string $language
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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
        'is_trusted',
        'is_untrusted',
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
     * @return  Login
     */
    public function login()
    {
        $relation = $this->hasOne(Login::class);
        $relation->orderBy('created_at', 'desc');
        return $relation;
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
