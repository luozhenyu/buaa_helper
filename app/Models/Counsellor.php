<?php

namespace App\Models;

use App\Models\ModelInterface\HasDepartmentAvatar;

class Counsellor extends Admin implements HasDepartmentAvatar
{
    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'Counsellor',
            'display_name' => '院系辅导员',
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
