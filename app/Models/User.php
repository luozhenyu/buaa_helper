<?php

namespace App\Models;

use Exception;
use Faker\Provider\Uuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Zizaco\Entrust\EntrustFacade;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'email', 'phone', 'department_id', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified', 'phone_verified',
    ];

    /**
     * 此用户拥有的设备
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany('App\Models\Device', 'user_id', 'id');
    }

    /**
     * 此用户所属department
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    /**
     * 此用户收到的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receivedNotifications()
    {
        return $this->belongsToMany('App\Models\Notification', 'notification_user', 'user_id', 'notification_id')
            ->withPivot('read_at', 'stared_at', 'deleted_at');
    }

    /**
     * 此用户收藏的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function staredNotifications()
    {
        return $this->receivedNotifications()
            ->wherePivot('stared_at', '!=', null);
    }

    /**
     * 此用户已阅读的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readNotifications()
    {
        return $this->receivedNotifications()
            ->wherePivot('read_at', '!=', null);
    }

    /**
     * 此用户未阅读的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notReadNotifications()
    {
        return $this->receivedNotifications()
            ->wherePivot('read_at', null);
    }

    /**
     * 此用户上传的文件
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany('App\Models\File', 'user_id', 'id');
    }

    /**
     *  此用户提出的问题
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inquiries()
    {
        return $this->hasMany('App\Models\Inquiry', 'user_id', 'id');
    }

    /**
     *  此用户回复的问题
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inquiryReplies()
    {
        return $this->hasMany('App\Models\InquiryReply', 'user_id', 'id');
    }

    /**
     * @param array $condition
     * @param int|null $limit
     * @return mixed
     * @throws Exception
     */
    public static function select(array $condition, int $limit = null)
    {
        $query = new static;
        if (!is_null($limit)) {
            $query = $query->whereHas('department', function ($subQuery) use ($limit) {
                $subQuery->where('number', $limit);
            });
        }

        if (!key_exists('range', $condition)) {
            throw new Exception('range键不存在');
        }

        $query = $query->whereHas(function ($subQuery) use ($condition) {
            list($ALL, $ALL_COLLEGE, $ALL_OFFICE) = [-1, 0, 100];

            foreach ($condition['range'] as $item) {
                $department = $item['department'] ?? null;
                if (is_null($department)) {
                    throw new Exception("department键不存在");
                }
                $limit = [['properties.name', 'grade']];
                if ($department === $ALL) {//所有人
                    $limit[] = ['departments.number', '>', 0];
                } else if ($department === $ALL_COLLEGE) {//所有院系
                    $limit[] = ['departments.number', '<', 100];
                } else if ($department === $ALL_OFFICE) {//所有部门
                    $limit[] = ['departments.number', '>', 100];
                } else {//指定院系或部门
                    $limit[] = ['departments.number', $department];
                }

                $grade = $item['grade'] ?? null;
                if ($grade) {
                    $limit[] = ['property_values.name', $grade];
                }
                $subQuery = $subQuery->orWhere($limit);
            }
        });

        return;

        $query = static::join('departments', 'users.department_id', 'departments.id')
            ->join('properties', 'property_user.property_id', 'properties.id')
            ->join('property_values', 'property_user.property_value_id', 'property_values.id')
            //role
            ->join('role_user', 'users.id', 'role_user.user_id')
            ->join('roles', 'roles.id', 'role_user.role_id');
        if (!is_null($limit)) {
            $query = $query->where('departments.number', $limit);
        }

        if (!key_exists('range', $condition)) {
            throw new Exception("range键不存在");
        }
        $query = $query->where(function ($subQuery) use ($condition) {
            list($ALL, $ALL_COLLEGE, $ALL_OFFICE) = [-1, 0, 100];

            foreach ($condition['range'] as $item) {
                $department = $item['department'] ?? null;
                if (is_null($department)) {
                    throw new Exception("department键不存在");
                }
                $limit = [['properties.name', 'grade']];
                if ($department === $ALL) {//所有人
                    $limit[] = ['departments.number', '>', 0];
                } else if ($department === $ALL_COLLEGE) {//所有院系
                    $limit[] = ['departments.number', '<', 100];
                } else if ($department === $ALL_OFFICE) {//所有部门
                    $limit[] = ['departments.number', '>', 100];
                } else {//指定院系或部门
                    $limit[] = ['departments.number', $department];
                }

                $grade = $item['grade'] ?? null;
                if ($grade) {
                    $limit[] = ['property_values.name', $grade];
                }
                $subQuery = $subQuery->orWhere($limit);
            }
        });

        $properties = $condition['property'] ?? [];

        foreach ($properties as $key => $value) {
            $query = $query->whereExists(function ($query) use ($key, $value) {
                $query->select(DB::raw(1))
                    ->from('property_user')
                    ->join('properties', 'property_user.property_id', 'properties.id')
                    ->join('property_values', 'property_user.property_value_id', 'property_values.id')
                    ->whereRaw('property_user.user_id = users.id')
                    ->where(function ($subQuery) use ($key, $value) {
                        foreach ((array)$value as $opt) {
                            $subQuery = $subQuery->orWhere([
                                ['properties.name', $key],
                                ['property_values.name', $opt]
                            ]);
                        }
                    });
            });
        }
        $isAdmin = (int)EntrustFacade::hasRole('admin');
        $url = url('/account_manager') . '/';
        $query = $query->select(
            'departments.number as department',
            'departments.name as department_name',
            'users.number as number',
            'users.name as name',
            'property_values.display_name as grade',
            'roles.display_name as role',
            DB::raw("if({$isAdmin},concat('{$url}',users.id),null) as url")
        );

        $orderBy = $condition['orderBy'] ?? null;
        $sort = $condition['sort'] ?? "asc";

        if ($orderBy) {
            $query = $query->orderBy($orderBy, $sort);
        }

        return $query;
    }


    public static function boot()
    {
        parent::boot();

        static::creating(function (User $user) {
            if (User::where('number', $user->number)->count() > 0) {
                throw new Exception('Duplicated number');
            }
        });

        //deleting被Entrust使用
        static::deleted(function (User $user) {
            $user->roles()->detach();
            $user->receivedNotifications()->detach();

            foreach ($user->devices as $device) {
                $device->delete();
            }

            foreach ($user->files as $file) {
                $file->delete();
            }

            foreach ($user->inquiries as $inquiry) {
                $inquiry->delete();
            }

            foreach ($user->inquiryReplies as $inquiryReply) {
                $inquiryReply->delete();
            }
        });
    }

    /**
     * 将User类型向下转化为子类
     * @param User $user
     * @return mixed
     */
    public static function downcasting(User $user)
    {
        $id = $user->id;
        $table = $user->getTable();
        $ret = DB::table($table)->join('pg_class', "{$table}.tableoid", 'pg_class.oid')
            ->where('id', $id)->select('pg_class.relname')->first();

        switch ($ret->relname) {
            case 'super_admins':
                return SuperAdmin::find($id);

            case 'department_admins':
                return DepartmentAdmin::find($id);

            case 'counsellors':
                return Counsellor::find($id);

            case 'students':
                return Student::find($id);

            default:
                return User::find($id);
        }
    }
}
