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
        return $this->belongsToMany('App\Models\User')
            ->wherePivot('star', true)
            ->withPivot('star', 'stared_at');
    }

    /**
     * 已阅读此通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function readUsers()
    {
        return $this->belongsToMany('App\Models\User')
            ->wherePivot('read', true)
            ->withPivot('read', 'read_at');
    }

    /**
     * 未阅读此通知的用户
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function notReadUsers()
    {
        return $this->belongsToMany('App\Models\User')
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
