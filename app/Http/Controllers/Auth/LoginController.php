<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        $username = Cas::getCurrentUser();
        $attributes = Cas::getAttributes();

        echo "<p>{$username}</p>";

        dd($attributes);


//        if (!$user = User::findAndDowncasting($number)) {
//            $user = Student::create([
//                'number' => $request->input('number'),
//                'name' => $request->input('name'),
//                'department_id' => $department->id,
//            ]);
//        }

        Cas::logout();
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
