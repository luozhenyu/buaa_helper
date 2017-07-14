<?php

namespace App\Models;

use App\Models\ModelInterface\HasDepartmentAvatar;

class DepartmentAdmin extends Admin implements HasDepartmentAvatar
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, [
            'view_all_student',
        ]);
    }

    public static function boot()
    {
        parent::boot();
    }

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'DepartmentAdmin',
            'display_name' => '部门管理员',
        ];
    }

    /**
     * 设置用户的头像
     * @param File $file
     * @return void
     */
    public function setAvatar(File $file)
    {
        $department = $this->department;
        $department->customAvatar()->associate($file);
        $department->save();
    }

    /**
     * 获得用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        $department = $this->department;
        return $department->avatar->url;
    }

    /**
     * 恢复默认头像
     * @return mixed
     */
    public function restore()
    {
        $department = $this->department;
        $department->customAvatar()->dissociate();
        $department->save();
    }
}
