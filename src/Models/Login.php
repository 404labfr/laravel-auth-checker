<?php

namespace Lab404\AuthChecker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @package Lab404\AuthChecker\Models
 * @property int $id
 * @property \Lab404\AuthChecker\Models\Device $device
 * @property int $device_id
 * @property \Illuminate\Contracts\Auth\Authenticatable $user
 * @property int $user_id
 * @property string $user_type
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
    /** @var array $with */
    protected $with = ['device'];
    /** @var array $casts */
    protected $casts = [
        'user_id' => 'integer',
        'user_type' => 'string',
        'device_id' => 'integer',
        'ip_address' => 'string',
    ];
    /** @var array $fillable */
    protected $fillable = [
        'user_id',
        'user_type',
        'ip_address',
        'created_at',
        'type',
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function device(): BelongsTo
    {
        $model = config('auth-checker.models.device') ?? Device::class;

        return $this->belongsTo($model);
    }
}
