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

    public function delete()
    {
        $this->access_tokens()->delete();
        $this->received_notifications()->detach();
        $this->stared_notifications()->detach();
        return parent::delete();
    }

    /**
     * Find all access tokens the user has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function access_tokens()//
    {
        return $this->hasMany('App\Models\AccessToken');
    }

    public function received_notifications()//checked
    {
        return $this->belongsToMany('App\Models\Notification');
    }

    public function stared_notifications()
    {
        return $this->belongsToMany('App\Models\Notification', 'stars')->withTimestamps();
    }

    /**
     * Generate and use a new appToken.
     *
     * @param integer $expires_in
     * @return string
     */
    public function createAccessToken($expires_in = 0)//checked
    {
        do {
            $uuid = Uuid::uuid();
        } while (AccessToken::where('access_token', $uuid)->count() > 0);

        return $this->access_tokens()->create([
            'access_token' => $uuid,
            'expires_in' => $expires_in,
        ]);
    }

    public function department()//
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function written_notifications()//
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function notifications()//checked
    {
        return $this->department->received_notifications->merge($this->received_notifications);
    }

    public function updatePassword($str)
    {
        $this->access_tokens()->delete();
        $this->password = is_null($str) ? null : bcrypt($str);
    }

    public function read_notifications()
    {
        return $this->belongsToMany('App\Models\Notification', 'reads')->withTimestamps();
    }

    public function inquiries()
    {
        return $this->hasMany('App\Models\Inquiry');
    }
}
