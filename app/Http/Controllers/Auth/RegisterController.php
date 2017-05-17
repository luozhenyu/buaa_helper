<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @param string $user_number
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm($user_number = null)
    {
        if (is_null($user_number)) {
            return view('auth.register');
        }

        return view('auth.register', [
            'user' => User::where(['number' => $user_number, 'password' => null])->firstOrFail(),
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @param string $user_number
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request, $user_number = null)
    {
        if (is_null($user_number)) {
            $this->validate($request, [
                'number' => 'required|numeric|exists:users,number,password,NULL',
            ]);
            return redirect('register/' . $request->input('number'));
        }

        $this->validator($request->all())->validate();

        $user = User::where(['number' => $user_number, 'password' => null])->firstOrFail();
        $user->updatePassword($request->input('password'));
        $user->save();

        event(new Registered($user));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'password' => 'required|min:6|confirmed',
        ]);
    }
}
