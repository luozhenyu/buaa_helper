<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

class MobileController extends Controller
{
    /**
     * APIController constructor.
     * Need to pass api auth.
     *
     * @param Request $request
     */
    function __construct(Request $request)
    {
        $this->middleware('auth.api', ['except' => ['showRegistrationForm', 'register']]);
    }

    public function showRegistrationForm($user_number = null)
    {
        if (is_null($user_number)) {
            return view('mobile.register');
        }

        return view('mobile.register', [
            'user' => User::where(['number' => $user_number, 'password' => null])->firstOrFail(),
        ]);
    }

    public function register(Request $request, $user_number = null)
    {
        if (is_null($user_number)) {
            $this->validate($request, [
                'number' => 'required|numeric|exists:users,number,password,NULL',
            ]);
            return redirect('/mobile/register/' . $request->input('number'));
        }

        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where(['number' => $user_number, 'password' => null])->firstOrFail();
        $user->updatePassword($request->input('password'));
        $user->save();
        return response()->json(['msg' => '注册成功']);
    }

    public function account(Request $request)
    {
        $auth_user = $request->get('user');
        $access_token = $request->input('access_token');

        return view('mobile.account.index', [
            'auth_user' => $auth_user,
            'access_token' => $access_token,
        ]);
    }

    public function showProfileForm(Request $request)
    {
        $auth_user = $request->get('user');
        $access_token = $request->input('access_token');

        return view('mobile.account.info', [
            'auth_user' => $auth_user,
            'access_token' => $access_token,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $auth_user = $request->get('user');
        $access_token = $request->input('access_token');
        $this->validate($request, [
            'email' => 'email|unique:users,email,' . $auth_user->id,
            'phone' => 'digits:11|unique:users,phone,' . $auth_user->id,
        ]);

        if ($request->has('email')) {
            $auth_user->email = $request->input('email');
        }
        if ($request->has('phone')) {
            $auth_user->phone = $request->input('phone');
        }
        $auth_user->save();
        return redirect('/mobile/account?access_token=' . $access_token);
    }

    public function showPasswordForm(Request $request)
    {
        $auth_user = $request->get('user');
        $access_token = $request->input('access_token');

        return view('mobile.account.password', [
            'auth_user' => $auth_user,
            'access_token' => $access_token,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $auth_user = $request->get('user');


        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (Hash::check($request->input('old_password'), $auth_user->password)) {
            $auth_user->updatePassword($request->input('password'));
            $auth_user->save();

            return response('修改密码成功，请重新登录');
        }
        return redirect()->back()->withErrors([
            'old_password' => Lang::get('auth.failed'),
        ]);
    }

    public function inquiryIndex(Request $request)
    {
        $auth_user = $request->get('user');
        $access_token = $request->input('access_token');

        return view('mobile.inquiry.index', [
            'auth_user' => $auth_user,
            'access_token' => $access_token,
        ]);
    }

    public function inquiryCreate(Request $request)
    {
        $access_token = $request->input('access_token');

        return view('mobile.inquiry.create', [
            'access_token' => $access_token,
        ]);
    }

    public function inquiryStore(Request $request)
    {
        $this->validate($request, [
            'title' => 'max:20',
            'type' => 'required|max:10',
            'content' => 'required|min:10|max:1000',
        ]);

        $auth_user = $request->get('user');
        $access_token = $request->input('access_token');

        $title = $request->input('title');
        $type = $request->input('type');
        $content = $request->input('content');

        $auth_user->inquiries()->create([
            'title' => empty($title) ? substr($content, 0, 10) : $title,
            'type' => $type,
            'content' => $content,
            'finished' => false,
        ]);
        return redirect('/mobile/inquiry?access_token=' . $access_token);
    }

    public function inquiryShow(Request $request, $inquiry_id)
    {
        $auth_user = $request->get('user');
        $access_token = $request->input('access_token');
        $inquiry = $auth_user->inquiries()->findOrFail($inquiry_id);

        return view('mobile.inquiry.show', [
            'access_token' => $access_token,
            'inquiry' => $inquiry,
        ]);
    }
}