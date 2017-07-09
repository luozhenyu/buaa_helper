<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'description',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Department $department) {
            foreach ($department->users as $user) {
                $user->delete();
            }

            foreach ($department->inquiries as $inquiry) {
                $inquiry->delete();
            }
        });
    }

    /**
     * 部门的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->customAvatar ? $this->customAvatar() : $this->defaultAvatar();
    }

    /**
     * 部门的自定义头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customAvatar()
    {
        return $this->belongsTo('App\Models\File', 'file_id', 'id');
    }

    /**
     * 部门的默认头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function defaultAvatar()
    {
        return $this->belongsTo('App\Models\File', 'default_file_id', 'id');
    }

    /**
     * 此department拥有的用户
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    /**
     * 此department拥有的问题
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inquiries()
    {
        return $this->hasMany('App\Models\Inquiry');
    }
}
