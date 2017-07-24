<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Xavrsl\Cas\Facades\Cas;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function cas(Request $request)
    {
        if (!Cas::isAuthenticated()) {
            Cas::authenticate();
        }

        $attributes = Cas::getAttributes();
        $number = intval($attributes['employeeNumber']);

        if (!$user = User::findAndDowncasting($number)) {
            $username = Cas::getCurrentUser();

            $user = Student::create([
                'number' => $number,
                'name' => $username,
                'department_id' => 21,
            ]);
        }
        Auth::login($user);
        dd(url());
        Cas::logout(['service' => url()]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $user = $request->input($this->username());
        if (ctype_digit($user)) {
            if (strlen($user) === 11) {
                $credential_type = 'phone';
            } else {
                $credential_type = 'number';
            }
        } else {
            $credential_type = 'email';
        }

        return [
            $credential_type => $user,
            'password' => $request->input('password'),
        ];
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'user';
    }
}
