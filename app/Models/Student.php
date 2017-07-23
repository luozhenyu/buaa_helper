<?php

namespace App\Models;

use App\Models\ModelInterface\HasPersonalAvatar;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class Student extends User implements HasPersonalAvatar
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'email', 'phone', 'department_id', 'password', 'avatar',
        'grade', 'class', 'political_status', 'native_place', 'financial_difficulty',
    ];

    public static function boot()
    {
        parent::boot();
    }

    /**
     * @param array $condition
     * @param Authenticatable $authUser
     * @return mixed
     * @throws Exception
     */
    public static function selectByCondition(array $condition, Authenticatable $authUser)
    {
        $query = new static;

        if ($authUser->hasPermission('view_all_student')) {
        } else if ($authUser->hasPermission('view_owned_student')) {
            $limit = $authUser->department->number;
            $query = $query->whereHas('department', function ($query) use ($limit) {
                $query->where('number', $limit);
            });
        } else {
            return $query->whereRaw('FALSE');
        }

        if (key_exists('search', $condition)) {
            $search = $condition['search'];
            $query = $query->where(function ($query) use ($search) {
                $query->where(DB::raw('cast(number as text)'), 'like', "%{$search}%");
            });
        }

        if (key_exists('range', $condition)) {
            $range = $condition['range'];
            $query = $query->where(function ($query) use ($range) {
                foreach ($range as $item) {
                    $department = $item['department'] ?? false;
                    $grade = $item['grade'] ?? false;

                    $query = $query->orWhere(function ($query) use ($department, $grade) {
                        //自定义分组，grade为0
                        if ($grade === 0) {
                            $query->where(function ($query) use ($department) {
                                $query = $query->whereHas('belongsToGroups', function ($query) use ($department) {
                                    $query->where('id', $department);
                                });
                                return $query;
                            });
                        } else {
                            $query->when($department, function ($query) use ($department) {
                                return $query->whereHas('department', function ($query) use ($department) {
                                    $query->where('number', $department);
                                });
                            })->when($grade, function ($query) use ($grade) {
                                return $query->where('grade', $grade);
                            });
                        }
                    });
                }
            });
        }

        if (key_exists('property', $condition)) {
            foreach ($condition['property'] as $key => $value)
                $query = $query->where($key, $value);
        }
        return $query;
    }

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'Student',
            'display_name' => '学生',
        ];
    }

    public function needCompleteInformation()
    {
        if (parent::needCompleteInformation()) {
            return true;
        }
        $attrs = ['grade', 'class', 'political_status', 'native_place', 'financial_difficulty'];
        foreach ($attrs as $attr) {
            if (empty($this->$attr)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 设置用户的头像
     * @param File $file
     * @return void
     */
    public function setAvatar(File $file)
    {
        $this->avatar()->associate($file);
    }

    /**
     * 用户的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo('App\Models\File', 'avatar', 'id');
    }

    /**
     * 获得用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        $domain = env('APP_URL');
        $domain .= (substr($domain, -1) === '/' ? '' : '/');
        $avatar = $this->avatar;
        return $avatar ? $avatar->url : $domain . 'img/favicon.png';
    }

}
