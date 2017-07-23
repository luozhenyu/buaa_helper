<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'user_id', 'department_id', 'start_date', 'finish_date', 'important', 'excerpt', 'target', 'content',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'start_date', 'finish_date',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Notification $notification) {
            $notification->notifiedUsers()->detach();
            $notification->files()->detach();
        });
    }

    /**
     * 所有收到通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function notifiedUsers()
    {
        return $this->belongsToMany('App\Models\User')
            ->withPivot('stared_at', 'read_at');
    }

    /**
     * 此通知的附件
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany('App\Models\File');
    }

    public function publish()
    {
        $this->published_at = Carbon::now();
        $this->save();
    }

    public function isPublished()
    {
        return (bool)$this->published_at;
    }

    /**
     * 此通知的作者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 此通知所属部门
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    /**
     * 收藏此通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function staredUsers()
    {
        return $this->notifiedUsers()
            ->wherePivot('stared_at', '!=', null);
    }

    /**
     * 已阅读此通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function readUsers()
    {
        return $this->notifiedUsers()
            ->wherePivot('read_at', '!=', null);
    }

    /**
     * 未阅读此通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function notReadUsers()
    {
        return $this->notifiedUsers()
            ->wherePivot('read_at', null);
    }
}
