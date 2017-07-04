<?php

namespace App\Models;

class Student extends User
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

        static::created(function (Student $user) {
            $role = Role::where('name', 'student')->firstOrFail();
            $user->attachRole($role);
        });
    }
}
