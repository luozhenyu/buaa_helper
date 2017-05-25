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
        'sha1', 'fileName', 'path',
    ];

    /**
     * 此文件所属用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
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
}
