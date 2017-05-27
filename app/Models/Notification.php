<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'user_id', 'department_id', 'start_time', 'end_time', 'important', 'content',
    ];

    protected $dates = ['created_at', 'updated_at', 'start_time', 'end_time'];

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
     * 所有收到通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function notifiedUsers()
    {
        return $this->belongsToMany('App\Models\User')
            ->withPivot('star', 'read', 'stared_at', 'read_at');
    }

    /**
     * 收藏此通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function staredUsers()
    {
        return $this->notifiedUsers()
            ->wherePivot('star', true);
    }

    /**
     * 已阅读此通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function readUsers()
    {
        return $this->notifiedUsers()
            ->wherePivot('read', true);
    }

    /**
     * 未阅读此通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function notReadUsers()
    {
        return $this->notifiedUsers()
            ->wherePivot('read', false);
    }

    /**
     * 此通知的附件
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function files()
    {
        return $this->belongsToMany('App\Models\File');
    }
}
