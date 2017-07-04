<?php

namespace App\Models;

use App\Http\Controllers\FileController;
use Exception;
use Illuminate\Support\Facades\Storage;

class DepartmentAdmin extends Admin
{
    public function setDepartmentAvatar(File $file)
    {
        $department = $this->department;
        $department->avatarFile()->associate($file);
        $department->save();
    }

    public static function boot()
    {
        parent::boot();

        static::created(function (DepartmentAdmin $user) {
            $role = Role::where('name', 'departmentAdmin')->firstOrFail();
            $user->attachRole($role);
        });
    }
}
