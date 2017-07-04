<?php

namespace App\Models;

class Counsellor extends Admin
{
    public static function boot()
    {
        parent::boot();

        static::created(function (Counsellor $user) {
            $role = Role::where('name', 'counsellor')->firstOrFail();
            $user->attachRole($role);
        });
    }
}
