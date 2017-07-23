<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;


class Admin extends User
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, [
            'view_owned_student',
            'create_notification',
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
    public static function selectByCondition(array $condition, Authenticatable $authUser)
    {
        $query = new static;

        if (!$authUser->hasPermission('view_admin')) {
            return $query->whereRaw('FALSE');
        }

        if (key_exists('search', $condition)) {
            $search = $condition['search'];
            $query = $query->where(function ($query) use ($search) {
                $query->where(DB::raw('cast(number as text)'), 'like', "%{$search}%");
            });
        }

        if (key_exists('range', $condition)) {
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
        }
        return $query;
    }

    public function selectData()
    {
        $college = [];
        foreach (Department::where('number', '<', 100)->get() as $department) {
            $college [] = [
                'name' => $department->number,
                'display_name' => $department->display_name,
            ];
        }

        //grade
        $grade = Property::where('name', 'grade')->firstOrFail();

        $students[] = ['name' => ',', 'display_name' => '全校学生'];
        $students = array_merge($students, $grade->propertyValues->map(function ($item, $key) use ($college) {
            $gradeNumber = $item->name;
            $tot[] = ['name' => ",{$gradeNumber}", 'display_name' => '本年级学生'];

            return [
                'display_name' => $item->display_name,
                'children' => array_merge($tot, collect($college)->map(function ($item, $key) use ($gradeNumber) {
                    return [
                        'name' => "{$item['name']},{$gradeNumber}",
                        'display_name' => $item['display_name'],
                    ];
                })->toArray()),
            ];
        })->toArray());
        //group
        $groups = Auth::user()->groups()->orderBy('name')->get();
        $students[] = [
            'display_name' => '我的分组',
            'children' => $groups->map(function ($item, $key) {
                return [
                    'name' => "{$item->id},0",
                    'display_name' => $item->name,
                ];
            })->toArray()
        ];

        //properties
        $propertyNames = ['political_status', 'financial_difficulty'];
        $properties = [];
        foreach ($propertyNames as $propertyName) {
            $property = Property::where('name', $propertyName)->firstOrFail();
            $propertyValues = $property->propertyValues->map(function ($item, $key) {
                return [
                    'name' => $item->name,
                    'display_name' => $item->display_name,
                ];
            })->toArray();
            $properties[] = [
                'name' => $property->name,
                'display_name' => $property->display_name,
                'children' => $propertyValues,
            ];
        }

        return [
            'department' => $students,
            'property' => $properties,
        ];
    }

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'Admin',
            'display_name' => '管理员',
        ];
    }

    /**
     * 此用户编写的通知草稿
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function draftNotifications()
    {
        return $this->writtenNotifications()
            ->where('published_at', null);
    }

    /**
     * 此用户编写的通知
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function writtenNotifications()
    {
        return $this->hasMany('App\Models\Notification', 'user_id', 'id');
    }

    /**
     * 此用户发布的通知
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publishedNotifications()
    {
        return $this->writtenNotifications()
            ->where('published_at', '!=', null);
    }

}
