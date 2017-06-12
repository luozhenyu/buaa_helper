<?php

namespace App\Models;

use App\Http\Controllers\FileController;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'description',
    ];

    /**
     * 更改院系图标
     * @param string $relativePath
     */
    public function changeAvatar($relativePath)
    {
        $file = FileController::import($relativePath);
        $this->avatar()->associate($file);
        $this->save();
    }

    /**
     * 部门拥有的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatarFile()
    {
        return $this->belongsTo('App\Models\File', 'avatar', 'id');
    }

    /**
     * 用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return ($avatar = $this->avatarFile) ? $avatar->downloadInfo['url'] : null;
    }

    /**
     * 此department拥有的用户
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Department $department) {
            foreach ($department->users as $user) {
                $user->delete();
            }
        });
    }
}
