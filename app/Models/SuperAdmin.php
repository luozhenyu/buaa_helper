<?php

namespace App\Models;

class SuperAdmin extends Admin
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'name', 'email', 'phone', 'department_id', 'password', 'avatar'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (SuperAdmin $user) {
            $role = Role::where('name', 'superAdmin')->firstOrFail();
            $user->attachRole($role);
        });
    }
}
