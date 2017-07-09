<?php

namespace App\Models\ModelInterface;

interface HasDepartmentAvatar extends HasAvatar
{
    /**
     * 恢复默认头像
     * @return mixed
     */
    public function restore();
}