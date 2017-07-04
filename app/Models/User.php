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
     * 此用户的头像
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatarFile()
    {
        return $this->belongsTo('App\Models\File', 'avatar', 'id');
    }

    /**
     * 用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        $domain = env('APP_URL');
        $domain .= (substr($domain, -1) === '/' ? '' : '/');
        return ($avatar = $this->avatarFile) ? $avatar->downloadInfo['url'] : $domain . 'img/favicon.png';
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
    public function questions()
    {
        return $this->hasMany('App\Models\Question', 'user_id', 'id');
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

            foreach ($user->questions as $question) {
                $question->delete();
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
