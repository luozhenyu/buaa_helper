<?php

namespace App\Models;

class Admin extends User
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, [
            'view_owned_user', 'modify_owned_user',
            'create_notification', 'modify_owned_notification',
        ]);
    }

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'Admin',
            'display_name' => '管理员',
        ];
    }

    /**
     * 此用户编写的通知
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function writtenNotifications()
    {
        return $this->hasMany('App\Models\Notification', 'user_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        //deleting被Entrust使用
        static::deleted(function (Admin $user) {
            foreach ($user->writtenNotifications as $notification) {
                $notification->delete();
            }
        });
    }
}
