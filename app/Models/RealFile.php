<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RealFile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sha1', 'mime', 'size',
    ];

    /**
     * 此文件保存的路径
     * @return string
     */
    public function getRelativePathAttribute()
    {
        $sha1 = $this->sha1;
        return 'upload/' . substr($sha1, 0, 2) . "/{$sha1}";
    }

    /**
     * 此文件保存的绝对路径
     * @return string
     */
    public function getAbsolutePathAttribute()
    {
        return Storage::url($this->relativePath);
    }

    /**
     * 此物理文件对应的虚拟文件
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany('App\Models\File');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function (RealFile $realFile) {
            if ($realFile->files->count() > 0) {
                return false;
            }
        });
    }
}
