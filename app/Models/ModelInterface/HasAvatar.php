<?php

namespace App\Models\ModelInterface;

use App\Models\File;

interface HasAvatar
{
    /**
     * 设置用户的头像
     * @param File $file
     * @return void
     */
    public function setAvatar(File $file);

    /**
     * 获得用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute();
}