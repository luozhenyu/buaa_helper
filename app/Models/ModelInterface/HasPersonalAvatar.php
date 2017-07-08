<?php

namespace App\Models\ModelInterface;

interface HasPersonalAvatar
{
    /**
     * 此用户的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar();

    /**
     * 此用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute();
}