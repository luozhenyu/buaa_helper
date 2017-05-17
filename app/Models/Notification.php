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

    public function user()//
    {
        return $this->belongsTo('App\User');
    }

    public function department()//
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function notified_departments()//checked
    {
        return $this->belongsToMany('App\Models\Department');
    }

    public function notified_users()//checked
    {
        return $this->belongsToMany('App\User');
    }

    public function notified_all()
    {
        $collec = $this->notified_users;
        foreach ($this->notified_departments as $department) {
            $collec = $collec->merge($department->users);
        }
        return $collec;
    }

    public function stared_users()
    {
        return $this->belongsToMany('App\User', 'stars')->withTimestamps();
    }

    public function read_users()
    {
        return $this->belongsToMany('App\User', 'reads')->withTimestamps();
    }


}
