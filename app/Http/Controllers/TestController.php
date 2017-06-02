<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class TestController extends Controller
{
    private $department;

//    public function __construct()
//    {
//        //department
//        $departmentName = Department::get()->mapWithKeys(function ($item, $key) {
//            return [$item->name => $item->id];
//        })->all();
//        $departmentNumber = Department::get()->filter(function ($item, $key) {
//            return $item->number < 100;
//        })->mapWithKeys(function ($item, $key) {
//            return [$item->number . '系' => $item->id];
//        })->all();
//        $this->department = array_merge($departmentName, $departmentNumber);
//    }

    public function test(Request $request)
    {

    }

//    function isDepartment($str)
//    {
//        return array_get($this->department, $str, false);
//    }
}
