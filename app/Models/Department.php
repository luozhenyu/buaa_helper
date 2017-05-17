<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'description',
    ];

    public function received_notifications()//checked
    {
        return $this->belongsToMany('App\Models\Notification');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
