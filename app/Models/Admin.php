<?php

namespace App\Models;

use Exception;
use \Illuminate\Contracts\Auth\Authenticatable;


class Admin extends User
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, [
            'view_owned_student',
            'create_notification', 'modify_owned_notification',
            'view_owned_inquiry',
        ]);
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Admin $user) {
            foreach ($user->writtenNotifications as $notification) {
                $notification->delete();
            }
        });
    }

    /**
     * @param array $condition
     * @param Authenticatable $authUser
     * @return mixed
     * @throws Exception
     */
    public static function select(array $condition, Authenticatable $authUser)
    {
        $query = new static;

        if (!$authUser->hasPermission('view_admin')) {
            return $query->whereRaw('FALSE');
        }

        if (!key_exists('range', $condition)) {
            throw new Exception('range键不存在');
        }
        $range = $condition['range'];

        $query = $query->where(function ($query) use ($range) {
            foreach ($range as $item) {
                if (!key_exists('department', $item)) {
                    throw new Exception('department键不存在');
                }
                $department = intval($item['department']);
                $query = $query->orWhereHas('department', function ($query) use ($department) {
                    $query->where('number', $department);
                });
            }
        });
        return $query;
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
}
