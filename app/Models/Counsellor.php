<?php

namespace App\Models;

use App\Models\ModelInterface\HasDepartmentAvatar;
use Illuminate\Support\Facades\Auth;

class Counsellor extends Admin implements HasDepartmentAvatar
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->permission = array_merge($this->permission, [
            'modify_owned_student',
        ]);
    }

    public static function boot()
    {
        parent::boot();
    }

    public function getRoleAttribute()
    {
        return (object)[
            'name' => 'Counsellor',
            'display_name' => '院系辅导员',
        ];
    }

    /**
     * 设置用户的头像
     * @param File $file
     * @return void
     */
    public function setAvatar(File $file)
    {
        $department = $this->department;
        $department->customAvatar()->associate($file);
        $department->save();
    }

    /**
     * 获得用户头像的链接
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        $department = $this->department;
        return $department->avatar->url;
    }

    /**
     * 恢复默认头像
     * @return mixed
     */
    public function restore()
    {
        $department = $this->department;
        $department->customAvatar()->dissociate();
        $department->save();
    }

    public function selectData()
    {
        $department = $this->department;

        $college [] = [
            'name' => $department->number,
            'display_name' => $department->display_name,
        ];

        //grade
        $grade = Property::where('name', 'grade')->firstOrFail();

        $students[] = ['name' => ',', 'display_name' => '所有学生'];
        $students = array_merge($students, $grade->propertyValues->map(function ($item, $key) use ($college) {
            $gradeNumber = $item->name;

            return [
                'display_name' => $item->display_name,
                'children' => collect($college)->map(function ($item, $key) use ($gradeNumber) {
                    return [
                        'name' => "{$item['name']},{$gradeNumber}",
                        'display_name' => $item['display_name'],
                    ];
                })->toArray(),
            ];
        })->toArray());
        //group
        $groups = Auth::user()->groups()->orderBy('name')->get();
        $students[] = [
            'display_name' => '我的分组',
            'children' => $groups->map(function ($item, $key) {
                return [
                    'name' => "{$item->id},0",
                    'display_name' => $item->name,
                ];
            })->toArray()
        ];

        //properties
        $propertyNames = ['political_status', 'financial_difficulty'];
        $properties = [];
        foreach ($propertyNames as $propertyName) {
            $property = Property::where('name', $propertyName)->firstOrFail();
            $propertyValues = $property->propertyValues->map(function ($item, $key) {
                return [
                    'name' => $item->name,
                    'display_name' => $item->display_name,
                ];
            })->toArray();
            $properties[] = [
                'name' => $property->name,
                'display_name' => $property->display_name,
                'children' => $propertyValues,
            ];
        }

        return [
            'department' => $students,
            'property' => $properties,
        ];
    }
}
