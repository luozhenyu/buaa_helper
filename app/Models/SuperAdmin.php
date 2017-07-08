<?php

namespace App\Models;

use App\Models\ModelInterface\HasPersonalAvatar;

class SuperAdmin extends Admin implements HasPersonalAvatar
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'email', 'phone', 'department_id', 'password', 'avatar'
    ];

    /**
     * 此用户的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo('App\Models\File', 'avatar', 'id');
    }

    /**
     * 用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        $domain = env('APP_URL');
        $domain .= (substr($domain, -1) === '/' ? '' : '/');
        return ($avatar = $this->avatar) ? $avatar->url : $domain . 'img/favicon.png';
    }

    public static function boot()
    {
        parent::boot();

        static::created(function (SuperAdmin $user) {
            $role = Role::where('name', 'superAdmin')->firstOrFail();
            $user->attachRole($role);
        });
    }
}
