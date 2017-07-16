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
     * 此department的显示文字
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        if ($this->number > 100) {
            return $this->name;
        }

        if ($this->number > 80) {
            return $this->name . '-北航学院';
        }

        return $this->number . '-' . $this->name;
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
     * 此department拥有的students
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany('App\Models\Student');
    }

    /**
     * 此department拥有的departmentAdmins
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function departmentAdmins()
    {
        return $this->hasMany('App\Models\DepartmentAdmin');
    }

    /**
     * 此department拥有的counsellor
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function counsellors()
    {
        return $this->hasMany('App\Models\Counsellor');
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
