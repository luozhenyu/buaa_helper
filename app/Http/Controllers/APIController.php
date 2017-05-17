<?php

namespace App\Http\Controllers;

use App\Func\ErrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    /**
     * APIController constructor.
     * Need to pass api auth.
     *
     * @param Request $request
     */
    function __construct(Request $request)
    {
        $this->middleware('auth.api', ['except' => ['login', 'register']]);
    }

    /**
     * Login with credentials and get an access token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::USER_ID_MISSING,
                'errmsg' => $validator->errors()->first(),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::PASSWORD_MISSING,
                'errmsg' => $validator->errors()->first(),
            ]);
        }

        if (!Auth::once($this->credentials($request))) {
            return response()->json([
                'errcode' => ErrCode::CREDENTIALS_ERROR,
                'errmsg' => Lang::get('auth.failed'),
            ]);
        }

        $access_token = Auth::user()->createAccessToken();
        return response()->json([
            'errcode' => ErrCode::OK,
            'access_token' => $access_token->access_token,
            'expires_in' => $access_token->expires_in,
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $user = $request->input('user');
        if ($this->isInteger($user)) {
            if (strlen($user) == 11) {
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
     * Finds whether the given variable is numeric.
     *
     * @param mixed $input
     * @return bool
     */
    protected function isInteger($input)
    {
        return ctype_digit(strval($input));
    }

    public function userInfo(Request $request)
    {
        $user = $request->get('user');

        return response()->json([
            'errcode' => ErrCode::OK,
            'user' => [
                'number' => $user->number,
                'name' => $user->name,
                'department' => $user->department_id,
                'department_name' => $user->department->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function listNotification(Request $request)
    {
        $user = $request->get('user');
        $notifications = [];
        foreach ($user->notifications() as $notification) {
            $notifications[] = [
                'id' => $notification->id,
                'updated_at' => intval($notification->updated_at->format('U')),
            ];
        }
        return response()->json([
            'errcode' => ErrCode::OK,
            'notifications' => $notifications,
        ]);
    }

    public function showNotification(Request $request, $notification_id)
    {
        $user = $request->get('user');
        $notification = $user->notifications()->find(intval($notification_id));

        if (is_null($notification)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }

        return response()->json([
            'errcode' => ErrCode::OK,
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'author' => $notification->user->name,
                'department' => $notification->department_id,
                'content' => $notification->content,
                'files' => $notification->files,
                'updated_at' => intval($notification->updated_at->format('U')),
            ]
        ]);
    }

    public function star(Request $request, $notification_id)
    {
        $user = $request->get('user');
        $notification = $user->notifications()->find($notification_id);

        if (is_null($notification)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }

        $notification->stared_users()->syncWithoutDetaching([$user->id]);
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Stared!',
        ]);
    }

    public function unstar(Request $request, $notification_id)
    {
        $user = $request->get('user');
        $notification = $user->notifications()->find($notification_id);

        if (is_null($notification)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }

        $notification->stared_users()->detach([$user->id]);
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Unstared!',
        ]);
    }

    public function stared(Request $request)
    {
        $user = $request->get('user');

        $notifications = [];
        foreach ($user->stared_notifications as $notification) {
            $notifications[] = [
                'id' => $notification->id,
                'stared_at' => intval($notification->pivot->updated_at->format('U')),
            ];
        }
        return response()->json([
            'errcode' => ErrCode::OK,
            'notifications' => $notifications,
        ]);
    }
}