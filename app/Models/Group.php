<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * 在数组中显示的属性
     *
     * @var array
     */
    protected $visible = ['id', 'name'];

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Group $group) {
            $group->users()->detach();
        });
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    /**
     * 该分组属于的用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
