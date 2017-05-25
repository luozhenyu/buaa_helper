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


    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    /**
     * Find all access tokens the user has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessTokens()
    {
        return $this->hasMany('App\Models\AccessToken');
    }

    public function writtenNotifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function receivedNotifications()
    {
        return $this->belongsToMany('App\Models\Notification')
            ->withPivot('star', 'read', 'stared_at', 'read_at');
    }

    public function staredNotifications()
    {
        return $this->belongsToMany('App\Models\Notification')
            ->wherePivot('star', true)
            ->withPivot('star', 'stared_at');
    }

    public function readNotifications()
    {
        return $this->belongsToMany('App\Models\Notification')
            ->wherePivot('read', true)
            ->withPivot('read', 'read_at');
    }

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

    public function files()
    {
        return $this->hasMany('App\Models\File');
    }
}
