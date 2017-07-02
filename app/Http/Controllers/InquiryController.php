<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Property;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $departments[] = ['name' => '-1', 'display_name' => '全校人员', 'children' => null];

        $office[] = ['name' => '100', 'display_name' => '所有部门'];
        $college[] = ['name' => '0', 'display_name' => '全年级学生'];

        foreach (Department::get() as $department) {
            $item = [
                'name' => (string)$department->number,
                'display_name' => $department->name,
            ];
            if ($department->number > 100) $office[] = $item;
            else $college [] = $item;
        }
        $departments[] = ['display_name' => '机关部处', 'children' => $office];

        //grade
        $grade = Property::where('name', 'grade')->firstOrFail();
        $students = array_merge([['name' => '0', 'display_name' => '全校学生', 'children' => null]],
            $grade->propertyValues->map(function ($item, $key) use ($college) {
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

        $departments[] = [
            'display_name' => '学生',
            'children' => $students,
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

        $data = [
            'department' => $departments,
            'property' => $properties,
        ];

        return view('inquiry.index', [
            'data' => $data,
        ]);
    }
}