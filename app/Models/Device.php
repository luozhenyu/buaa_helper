<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $ttl = 7;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'registrationID',
    ];

    /**
     * 该设备属于的用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 该设备是否有效
     * @return bool
     */
    public function isValid()
    {
        return $this->{Device::UPDATED_AT} >= $this->freshTimestamp()->subDays($this->ttl);
    }
}
