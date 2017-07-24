<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
        $number = ($attributes['employeeNumber']);

        if ($user = User::where('number', $number)->first()) {
            $user = User::downcasting($user);
        } else {
            $username = Cas::getCurrentUser();

            $user = Student::create([
                'number' => $number,
                'name' => $username,
                'department_id' => substr(strval($number), 2, 2),
            ]);
        }

        $this->guard()->login($user, true);
        return $this->sendLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        if (Cas::isAuthenticated()) {
            Cas::logout(['service' => url('/')]);
        }

        return redirect('/');
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
