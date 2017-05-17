<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'type', 'content', 'finished', 'user_id',
    ];

    public function author()
    {
        return $this->belongsTo('App\User');
    }
}
