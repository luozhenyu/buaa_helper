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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, [
            'create_user', 'delete_user', 'view_all_student', 'modify_all_student', 'view_admin', 'modify_admin',
            'delete_notification', 'modify_all_notification',
            'view_all_inquiry',
        ]);
    }

    public static function boot()
    {
        parent::boot();
    }

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'SuperAdmin',
            'display_name' => '超级管理员',
        ];
    }

    /**
     * 设置用户的头像
     * @param File $file
     * @return void
     */
    public function setAvatar(File $file)
    {
        $this->avatar()->associate($file);
    }

    /**
     * 用户的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo('App\Models\File', 'avatar', 'id');
    }

    /**
     * 获得用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        $domain = env('APP_URL');
        $domain .= (substr($domain, -1) === '/' ? '' : '/');
        $avatar = $this->avatar;
        return $avatar ? $avatar->url : $domain . 'img/favicon.png';
    }
}
