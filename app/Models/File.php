<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash', 'fileName',
    ];

    /**
     * 下载此文件相关信息
     * @return array
     */
    public function getDownloadInfoAttribute()
    {
        $domain = env('APP_URL');
        $domain .= (substr($domain, -1) === '/' ? '' : '/');
        $realFile = $this->realFile;
        return [
            'hash' => $this->hash,
            'fileName' => $this->fileName,
            'mime' => $realFile->mime,
            'size' => $realFile->size,
            'sha1' => $realFile->sha1,
            'url' => $domain . 'file/download/' . $this->hash,
        ];
    }

    /**
     * 此文件的物理文件
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function realFile()
    {
        return $this->belongsTo('App\Models\RealFile');
    }

    /**
     * 此文件所属用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 此文件所属通知
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function notifications()
    {
        return $this->belongsToMany('App\Models\Notification');
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function (File $file) {
            $file->notifications()->detach();

            $file->realFile->delete();
        });
    }
}
