<?php

namespace App\Models\ModelInterface;

use App\Models\File;

interface HasDepartmentAvatar
{
    /**
     * 此用户所属department的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departmentAvatar();

    /**
     * 设置此用户所属department的头像
     * @param File $file
     */
    public function setDepartmentAvatar(File $file);

    /**
     * 恢复此用户所属department的默认头像
     */
    public function restoreDefault();
}