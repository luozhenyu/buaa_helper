<?php

namespace App\Models;

use App\Models\ModelInterface\HasPersonalAvatar;

class Student extends User implements HasPersonalAvatar
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'email', 'phone', 'department_id', 'password', 'avatar',
        'grade', 'class', 'political_status', 'native_place', 'financial_difficulty',
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

        static::created(function (Student $user) {
            $role = Role::where('name', 'student')->firstOrFail();
            $user->attachRole($role);
        });
    }
}
