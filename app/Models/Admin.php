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

    /**
     * @param array $condition
     * @param int|null $limit
     * @return mixed
     * @throws Exception
     */
    public static function select(array $condition, int $limit = null)
    {
        $query = static::join('property_user', 'property_user.user_id', 'users.id')
            ->join('departments', 'users.department_id', 'departments.id')
            ->join('properties', 'property_user.property_id', 'properties.id')
            ->join('property_values', 'property_user.property_value_id', 'property_values.id')
            //role
            ->join('role_user', 'users.id', 'role_user.user_id')
            ->join('roles', 'roles.id', 'role_user.role_id');
        if (!is_null($limit)) {
            $query = $query->where('departments.number', $limit);
        }

        if (!key_exists('range', $condition)) {
            throw new Exception("range键不存在");
        }
        $query = $query->where(function ($subQuery) use ($condition) {
            list($ALL, $ALL_COLLEGE, $ALL_OFFICE) = [-1, 0, 100];

            foreach ($condition['range'] as $item) {
                $department = $item['department'] ?? null;
                if (is_null($department)) {
                    throw new Exception("department键不存在");
                }
                $limit = [['properties.name', 'grade']];
                if ($department === $ALL) {//所有人
                    $limit[] = ['departments.number', '>', 0];
                } else if ($department === $ALL_COLLEGE) {//所有院系
                    $limit[] = ['departments.number', '<', 100];
                } else if ($department === $ALL_OFFICE) {//所有部门
                    $limit[] = ['departments.number', '>', 100];
                } else {//指定院系或部门
                    $limit[] = ['departments.number', $department];
                }

                $grade = $item['grade'] ?? null;
                if ($grade) {
                    $limit[] = ['property_values.name', $grade];
                }
                $subQuery = $subQuery->orWhere($limit);
            }
        });

        $properties = $condition['property'] ?? [];

        foreach ($properties as $key => $value) {
            $query = $query->whereExists(function ($query) use ($key, $value) {
                $query->select(DB::raw(1))
                    ->from('property_user')
                    ->join('properties', 'property_user.property_id', 'properties.id')
                    ->join('property_values', 'property_user.property_value_id', 'property_values.id')
                    ->whereRaw('property_user.user_id = users.id')
                    ->where(function ($subQuery) use ($key, $value) {
                        foreach ((array)$value as $opt) {
                            $subQuery = $subQuery->orWhere([
                                ['properties.name', $key],
                                ['property_values.name', $opt]
                            ]);
                        }
                    });
            });
        }
        $isAdmin = (int)EntrustFacade::hasRole('admin');
        $url = url('/account_manager') . '/';
        $query = $query->select(
            'departments.number as department',
            'departments.name as department_name',
            'users.number as number',
            'users.name as name',
            'property_values.display_name as grade',
            'roles.display_name as role',
            DB::raw("if({$isAdmin},concat('{$url}',users.id),null) as url")
        );

        $orderBy = $condition['orderBy'] ?? null;
        $sort = $condition['sort'] ?? "asc";

        if ($orderBy) {
            $query = $query->orderBy($orderBy, $sort);
        }

        return $query;
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
