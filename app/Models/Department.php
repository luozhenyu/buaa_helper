<?php

namespace App\Models;

use App\Http\Controllers\FileController;
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

    /**
     * 部门的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
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

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Department $department) {
            foreach ($department->users as $user) {
                $user->delete();
            }
        });
    }
}
