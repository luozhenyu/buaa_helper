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

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function notifications()
    {
        return $this->belongsToMany('App\Models\Notification');
    }
}
