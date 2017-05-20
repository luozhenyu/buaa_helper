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
        'title', 'content', 'files', 'important', 'start_time', 'end_time', 'user_id', 'department_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function notifiedUsers()
    {
        return $this->belongsToMany('App\Models\User')
            ->withPivot('star', 'read', 'stared_at', 'read_at');
    }

    public function staredUsers()
    {
        return $this->belongsToMany('App\Models\User')
            ->wherePivot('star', true)
            ->withPivot('star', 'stared_at');
    }

    public function readUsers()
    {
        return $this->belongsToMany('App\Models\User')
            ->wherePivot('read', true)
            ->withPivot('read', 'read_at');
    }
}
