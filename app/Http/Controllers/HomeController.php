<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

class HomeController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['except' => 'viewIndex']);
    }

    /**
     * Show the index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewIndex()
    {
        return view('index');
    }

    public function viewAccount()
    {
        return view('account');
    }

    public function updateProfile(Request $request)
    {
        $auth_user = Auth::user();
        $this->validate($request, [
            'email' => 'nullable|email|max:40|unique:users,email,' . $auth_user->id,
            'phone' => 'nullable|phone|unique:users,phone,' . $auth_user->id,
        ]);

        if ($request->has('email')) {
            $auth_user->email = $request->input('email');
        }
        if ($request->has('phone')) {
            $auth_user->phone = $request->input('phone');
        }
        $auth_user->save();
        return redirect('/account');
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        $auth_user = Auth::user();
        if (Hash::check($request->input('old_password'), $auth_user->password)) {
            $auth_user->updatePassword($request->input('password'));
            $auth_user->save();

            return redirect('/account');
        }
        return redirect()->back()->withErrors([
            'old_password' => Lang::get('auth.failed'),
        ]);
    }

    public function viewInquiry()
    {
        return view('inquiry');
    }
}
