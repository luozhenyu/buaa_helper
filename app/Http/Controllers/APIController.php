<?php

namespace App\Http\Controllers;

use App\Func\ErrCode;
use Carbon\Carbon;
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
        $this->middleware('auth.api', ['except' => 'login']);
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
        if (ctype_digit($user)) {
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

    public function userInfo(Request $request)
    {
        $user = $request->get('user');
        return response()->json([
            'errcode' => ErrCode::OK,
            'user' => [
                'number' => $user->number,
                'name' => $user->name,
                'department' => $user->department->number,
                'department_name' => $user->department->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function modifyUserInfo(Request $request)
    {
        $user = $request->get('user');

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|max:40|unique:users,email,' . $user->id,
            'phone' => 'nullable|phone|unique:users,phone,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::FORM_ILLEGAL,
                'errmsg' => $validator->errors()->first(),
            ]);
        }

        if ($request->has('email')) {
            $user->email = $request->input('email');
        }
        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }
        $user->save();

        return response()->json([
            'errcode' => ErrCode::OK,
        ]);
    }

    public function listNotification(Request $request)
    {
        $user = $request->get('user');
        $notifications = $user->receivedNotifications->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'read' => (boolean)$item->pivot->read,
                'read_at' => $item->pivot->read_at,
                'star' => (boolean)$item->pivot->star,
                'stared_at' => $item->pivot->stared_at,
                'updated_at' => $item->updated_at->timestamp,
            ];
        });

        return response()->json([
            'errcode' => ErrCode::OK,
            'notifications' => $notifications,
        ]);
    }

    public function deletedNotification(Request $request)
    {
        $user = $request->get('user');
        $notifications = $user->deletedNotifications->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'read' => (boolean)$item->pivot->read,
                'read_at' => $item->pivot->read_at,
                'star' => (boolean)$item->pivot->star,
                'stared_at' => $item->pivot->stared_at,
                'updated_at' => $item->updated_at->timestamp,
            ];
        });

        return response()->json([
            'errcode' => ErrCode::OK,
            'notifications' => $notifications,
        ]);
    }

    public function showNotification(Request $request, $id)
    {
        $user = $request->get('user');

        if (!$notification = $user->receivedNotifications()->find($id)) {
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
                'department' => $notification->department->number,
                'department_name' => $notification->department->name,
                'start_time' => $notification->start_time->toDateTimeString(),
                'end_time' => $notification->end_time->toDateTimeString(),
                'content' => $notification->content,
                'files' => $notification->files->map(function ($item, $key) {
                    return $item->downloadInfo();
                }),
                'read' => (boolean)$notification->pivot->read,
                'read_at' => $notification->pivot->read_at,
                'star' => (boolean)$notification->pivot->star,
                'stared_at' => $notification->pivot->stared_at,
                'updated_at' => $notification->updated_at->timestamp,
            ]
        ]);
    }

    public function deleteNotification(Request $request, $id)
    {
        $user = $request->get('user');
        if (!$notification = $user->receivedNotifications()->find($id)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }
        $pivot = $notification->pivot;
        $pivot->deleted_at = Carbon::now();
        $pivot->save();
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Deleted!',
        ]);
    }

    public function restoreNotification(Request $request, $id)
    {
        $user = $request->get('user');
        if (!$notification = $user->deletedNotifications()->find($id)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }
        $pivot = $notification->pivot;
        $pivot->deleted_at = null;
        $pivot->save();
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Restored!',
        ]);
    }

    public function readNotification(Request $request, $id)
    {
        $user = $request->get('user');
        if (!$notification = $user->receivedNotifications()->find($id)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }
        $pivot = $notification->pivot;
        $pivot->read = true;
        $pivot->save();
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Read!',
        ]);
    }

    public function starNotification(Request $request, $id)
    {
        $user = $request->get('user');
        if (!$notification = $user->receivedNotifications()->find($id)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }
        $pivot = $notification->pivot;
        $pivot->star = true;
        $pivot->save();
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Stared!',
        ]);
    }

    public function unstarNotification(Request $request, $id)
    {
        $user = $request->get('user');
        if (!$notification = $user->receivedNotifications()->find($id)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }
        $pivot = $notification->pivot;
        $pivot->star = false;
        $pivot->save();
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Unstared!',
        ]);
    }
}