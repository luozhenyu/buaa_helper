<?php

namespace App\Models;

use App\Models\ModelInterface\HasPersonalAvatar;

class SuperAdmin extends Admin implements HasPersonalAvatar
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, [
            'create_user', 'delete_user', 'modify_all_user', 'view_all_user',
            'delete_notification', 'modify_all_notification',
        ]);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'email', 'phone', 'department_id', 'password', 'avatar'
    ];

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'SuperAdmin',
            'display_name' => '超级管理员',
        ];
    }

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
    }
}
