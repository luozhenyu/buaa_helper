<?php

namespace App\Models;

use App\Models\ModelInterface\HasDepartmentAvatar;

class DepartmentAdmin extends Admin implements HasDepartmentAvatar
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, [
            'view_all_user',
        ]);
    }

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'DepartmentAdmin',
            'display_name' => '部门管理员',
        ];
    }

    /**
     * 此用户所属department的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departmentAvatar()
    {
        $department = $this->department;
        return $department->avatar ?? $department->defaultAvatar;
    }

    /**
     * 设置此用户所属department的头像
     * @param File $file
     */
    public function setDepartmentAvatar(File $file)
    {
        $department = $this->department;
        $department->avatar()->associate($file);
        $department->save();
    }

    /**
     * 恢复此用户所属department的默认头像
     */
    public function restoreDefault()
    {
        $department = $this->department;
        $department->avatar()->dissociate();
        $department->save();
    }

    public static function boot()
    {
        parent::boot();
    }
}
