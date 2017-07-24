<?php

namespace App\Models;

use Exception;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'email', 'phone', 'department_id', 'password',
    ];

    use Notifiable;

    /**
     * 在数组中显示的属性
     *
     * @var array
     */
    protected $visible = ['number', 'name', 'email', 'phone', 'department_name', 'role_display_name', 'url'];

    /**
     * 在数组中追加显示的属性
     *
     * @var array
     */
    protected $appends = ['department_name', 'role_display_name', 'url'];

    protected $permission = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, ['a']);
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
            $user->receivedNotifications()->detach();

            $user->belongsToGroups()->detach();

            foreach ($user->groups as $group) {
                $group->delete();
            }

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
     * 此用户收到的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receivedNotifications()
    {
        return $this->belongsToMany('App\Models\Notification', 'notification_user', 'user_id', 'notification_id')
            ->where('published_at', '!=', null)
            ->withPivot('read_at', 'stared_at', 'deleted_at');
    }

    public function belongsToGroups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_user', 'user_id', 'group_id');
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
        return static::findAndDowncasting($id, $table);
    }

    public static function findAndDowncasting($id, $table = null)
    {
        $table = $table ?? (new static)->getTable();

        $ret = DB::table($table)->join('pg_class', "{$table}.tableoid", 'pg_class.oid')
            ->where('id', $id)->select('pg_class.relname')->first();

        $tableName = $ret ? $ret->relname : null;
        switch ($tableName) {
            case 'super_admins':
                return SuperAdmin::find($id);

            case 'department_admins':
                return DepartmentAdmin::find($id);

            case 'counsellors':
                return Counsellor::find($id);

            case 'students':
                return Student::find($id);

            default:
                return null;
        }
    }

    public function getDepartmentNameAttribute()
    {
        return $this->department->display_name;
    }

    public function getUrlAttribute()
    {
        $url = route('accountManager') . '/' . $this->id;

        $authUser = Auth::user();
        $user = $this;

        if ($user instanceof Student) {
            if ($authUser->hasPermission('modify_all_student')
                || ($authUser->hasPermission('modify_owned_student')
                    && $authUser->department_id === $user->department_id)
            ) {
                return $url;
            }
        } else if ($user instanceof Admin) {
            if ($authUser->hasPermission('modify_admin')) {
                return $url;
            }
        }
        return null;
    }

    /**
     * 检查用户是否具有权限
     * @param string|array $permissions
     * @return bool
     */
    public function hasPermission($permissions)
    {
        foreach ((array)$permissions as $permission) {
            if (in_array($permission, $this->permission, true)) {
                return true;
            }
        }
        return false;
    }

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'user',
            'display_name' => '用户',
        ];
    }

    public function getRoleDisplayNameAttribute()
    {
        return $this->role->display_name;
    }

    public function needCompleteInformation()
    {
        $attrs = ['email', 'phone'];
        foreach ($attrs as $attr) {
            if (empty($this->$attr)) {
                return true;
            }
        }
        return false;
    }

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

    public function groups()
    {
        return $this->hasMany('App\Models\Group', 'user_id', 'id');
    }
}
