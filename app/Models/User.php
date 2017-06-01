<?php

namespace App\Models;

use Faker\Provider\Uuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'email', 'phone', 'department_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 此用户所属department
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    /**
     * 此用户拥有的token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessTokens()
    {
        return $this->hasMany('App\Models\AccessToken');
    }

    /**
     * 此用户编写的通知
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function writtenNotifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    /**
     * 此用户收到的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receivedNotifications()
    {
        return $this->belongsToMany('App\Models\Notification')
            ->withPivot('read_at', 'stared_at', 'deleted_at');
    }

    /**
     * 此用户收藏的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function staredNotifications()
    {
        return $this->belongsToMany('App\Models\Notification')
            ->wherePivot('stared_at', '!=', null);
    }

    /**
     * 此用户已阅读的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readNotifications()
    {
        return $this->receivedNotifications()
            ->wherePivot('read_at', '!=', null);
    }

    /**
     * 此用户未阅读的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notReadNotifications()
    {
        return $this->receivedNotifications()
            ->wherePivot('read_at', null);
    }

    /**
     * 此用户上传的文件
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany('App\Models\File');
    }

    /**
     * 此用户拥有的properties
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function propertyValues()
    {
        return $this->hasMany('App\Models\PropertyValue');
    }

    /**
     * 修改用户密码并清除token
     * @param $str
     */
    public function updatePassword($str)
    {
        $this->accessTokens()->delete();
        $this->password = is_null($str) ? null : bcrypt($str);
    }

    /**
     * Generate and use a new appToken.
     *
     * @param integer $expires_in
     * @return string
     */
    public function createAccessToken($expires_in = 0)
    {
        do {
            $uuid = Uuid::uuid();
        } while (AccessToken::where('access_token', $uuid)->count() > 0);

        return $this->accessTokens()->create([
            'access_token' => $uuid,
            'expires_in' => $expires_in,
        ]);
    }
}
