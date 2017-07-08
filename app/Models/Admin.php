<?php

namespace App\Models;

class Admin extends User
{
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
