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
        'number', 'name', 'email', 'phone', 'avatar', 'department_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified', 'phone_verified'
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
    ];

    /**
     * 修改用户密码并清除token
     * @param $str
     */
    public function updatePassword($str)
    {
        $this->accessTokens()->delete();
        $this->password = is_null($str) ? null : bcrypt($str);
    }

    /**
     * 此用户拥有的token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessTokens()
    {
        return $this->hasMany('App\Models\AccessToken');
    }

    /**
     * 此用户拥有的设备
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany('App\Models\Device');
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
        return $this->belongsTo('App\Models\Department');
    }


    /**
     * 此用户拥有的properties
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function properties()
    {
        return $this->belongsToMany('App\Models\Property')
            ->withPivot('property_value_id')
            ->using('App\Models\PropertyUser');
    }

    /**
     * @param string $name
     * @return \App\Models\Property|null
     */
    private function getPropertyByName($name)
    {
        return $this->properties()->where('name', $name)->first();
    }

    /**
     * 获得此用户的某项属性值
     * @param string $name
     * @return string|null
     */
    public function getProperty($name)
    {
        if (!$property = $this->getPropertyByName($name)) {
            return null;
        }

        $propertyUser = $property->pivot;
        return $propertyUser->propertyValue->name;
    }

    /**
     * 删除此用户的某项属性值
     * @param string $name
     * @return bool
     */
    public function removeProperty($name)
    {
        if (!$property = $this->getPropertyByName($name)) {
            return false;
        }
        $property->pivot->delete();
        return true;
    }


    /**
     * 设置此用户的某项属性值
     * @param string $name
     * @param integer $value
     * @return bool
     */
    public function setProperty($name, $value)
    {
        if (empty($value)) {
            return $this->removeProperty($name);
        }

        if (!$property = Property::where('name', $name)->first()) {
            return false;
        }

        if (!$propertyValue = $property->propertyValues()->where('name', $value)->first()) {
            return false;
        }

        $this->properties()->syncWithoutDetaching([
            $property->id => [
                'property_value_id' => $propertyValue->id,
            ],
        ]);

        return true;
    }

    /**
     * 此用户编写的通知
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function writtenNotifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    /**
     * 此用户收到的通知
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function receivedNotifications()
    {
        return $this->belongsToMany('App\Models\Notification')
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
        return $this->hasMany('App\Models\File');
    }

    /**
     * Generate and use a new appToken.
     *
     * @param integer $expires_in
     * @return string
     */
    public function createAccessToken(int $expires_in = 0)
    {
        do {
            $uuid = Uuid::uuid();
        } while (AccessToken::where('access_token', $uuid)->count() > 0);

        return $this->accessTokens()->create([
            'access_token' => $uuid,
            'expires_in' => $expires_in,
        ]);
    }

    /**
     * @param array $condition
     * @param int|null $limit
     * @return mixed
     * @throws Exception
     */
    public static function select(array $condition, int $limit = null)
    {
        $query = static::join('property_user', 'property_user.user_id', 'users.id')
            ->join('departments', 'users.department_id', 'departments.id')
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

        //deleting被Entrust使用
        static::deleted(function (User $user) {
            $user->properties()->detach();
            $user->receivedNotifications()->detach();

            foreach ($user->accessTokens as $accessToken) {
                $accessToken->delete();
            }

            foreach ($user->devices as $device) {
                $device->delete();
            }

            foreach ($user->writtenNotifications as $notification) {
                $notification->delete();
            }

            foreach ($user->files as $file) {
                $file->delete();
            }
        });
    }
}
