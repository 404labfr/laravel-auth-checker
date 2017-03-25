<?php

namespace Lab404\AuthChecker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Login extends Model
{
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
        'ip_address',
        'created_at',
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
