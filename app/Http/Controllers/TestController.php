<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $user = new Student();
        dd($user->role->name);
//        $lim = json_decode("{\"range\":[{\"department\":-1}],\"property\":{}}", true);
//        $users = User::select($lim,21)->get();
//        dd($users);
    }
}
