<?php

namespace Lab404\AuthChecker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Login
 *
 * @package Lab404\AuthChecker\Models
 * @property int $id
 * @property \Lab404\AuthChecker\Models\Device $device
 * @property int $device_id
 * @property \Illuminate\Contracts\Auth\Authenticatable $user
 * @property int $user_id
 * @property string $ip_address
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Login extends Model
{
    /** @var string */
    const TYPE_LOGIN = 'auth';
    const TYPE_FAILED = 'failed';
    const TYPE_LOCKOUT = 'lockout';

    /** @var array */
    protected $with = ['device'];

    /** @var array */
    protected $casts = [
        'user_id' => 'integer',
        'device_id' => 'integer',
        'ip_address' => 'string',
    ];

    /** @var array */
    protected $fillable = [
        'user_id',
        'ip_address',
        'created_at',
        'type',
    ];

    /**
     * @param   void
     * @return  BelongsTo
     */
    public function user()
    {
        $model = config('auth.providers.users.model');

        return $this->belongsTo($model);
    }

    /**
     * @param   void
     * @return  BelongsTo
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
