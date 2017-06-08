<?php

namespace App\Http\Controllers;

use App\Func\ErrCode;
use App\Jobs\SendText;
use App\Models\City;
use App\Models\Device;
use App\Models\File;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;

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
        $this->middleware('auth.api', ['except' => ['index', 'login']]);
    }

    /**
     * 重定向到github
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(Request $request)
    {
        return redirect('https://github.com/luozhenyu/buaa_helper/blob/master/API%20Document.md');
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


    public function listDevice(Request $request)
    {
        $user = $request->get('user');
        $devices = $user->devices->map(function ($item, $key) {
            return [
                'registrationID' => $item->registrationID,
                'updated_at' => $item->updated_at->timestamp,
            ];
        });

        return response()->json([
            'errcode' => ErrCode::OK,
            'devices' => $devices,
        ]);
    }

    public function createDevice(Request $request)
    {
        $user = $request->get('user');

        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::FORM_ILLEGAL,
                'errmsg' => $validator->errors()->first(),
            ]);
        }
        $registrationID = $request->input('registration_id');
        $device = Device::firstOrNew(['registrationID' => $registrationID]);
        $device->user_id = $user->id;
        $device->touch();
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Created!',
        ]);
    }

    public function deleteDevice(Request $request)
    {
        $user = $request->get('user');

        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|exists:devices,registrationID,user_id,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::FORM_ILLEGAL,
                'errmsg' => $validator->errors()->first(),
            ]);
        }

        $registrationID = $request->input('registration_id');
        $device = Device::where('registrationID', $registrationID)->firstOrFail();
        $device->delete();

        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Deleted!',
        ]);
    }

    public function notifyDevice(Request $request)
    {
        $user = $request->get('user');

        $validator = Validator::make($request->all(), [
            'text' => 'required|max:70',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::FORM_ILLEGAL,
                'errmsg' => $validator->errors()->first(),
            ]);
        }

        dispatch(new SendText($request->input('text'), $user));

        return response()->json([
            'errcode' => ErrCode::OK,
        ]);
    }


    public function userInfo(Request $request)
    {
        $user = $request->get('user');
        return response()->json([
            'errcode' => ErrCode::OK,
            'user' => [
                'avatar' => $user->avatarUrl,
                'number' => $user->number,
                'name' => $user->name,
                'department' => $user->department->number,
                'department_name' => $user->department->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'grade' => $user->getProperty('grade'),
                'class' => $user->getProperty('class'),
                'political_status' => $user->getProperty('political_status'),
                'native_place' => ($place = City::where('code', $user->getProperty('native_place'))->first()) ? collect($place->tree())->map(function ($item, $index) {
                    return [
                        'code' => $item->code,
                        'name' => $item->name,
                    ];
                }) : [],
                'financial_difficulty' => $user->getProperty('financial_difficulty'),
            ],
        ]);
    }

    public function modifyUserAvatar(Request $request)
    {
        $user = $request->get('user');

        $uploadFile = $request->file('upload');

        if (!$uploadFile || !$uploadFile->isValid()) {
            return response()->json([
                'errcode' => ErrCode::FORM_ILLEGAL,
                'errmsg' => '文件上传失败',
            ]);
        }

        $fileName = $uploadFile->getClientOriginalName();
        if (strlen($fileName) > 200) {
            return response()->json([
                'errcode' => ErrCode::FORM_ILLEGAL,
                'errmsg' => '文件名最多为200字符',
            ]);
        }

        try {
            $img = Image::make($uploadFile)
                ->encode('png')
                ->resize(200, 200)
                ->save();
            $mime = $img->mime();
        } catch (NotReadableException $e) {
            return response()->json([
                "uploaded" => 0,
                "message" => "文件不是图片类型",
            ]);
        }

        $sha1 = sha1_file($uploadFile->getRealPath());
        if (!$file = File::where('sha1', $sha1)->first()) {
            $path = $uploadFile->storeAs('upload/' . substr($sha1, 0, 2), $sha1);
            $file = $user->files()->create([
                'sha1' => $sha1,
                'fileName' => $fileName,
                'mime' => $mime,
                'path' => $path,
            ]);
        }

        $user->avatar = $file->sha1;
        $user->save();

        return response()->json([
            'errcode' => ErrCode::OK,
        ]);
    }

    public function modifyUserInfo(Request $request)
    {
        $user = $request->get('user');

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|max:40|unique:users,email,' . $user->id,
            'phone' => 'nullable|phone|unique:users,phone,' . $user->id,

            'grade' => 'nullable|exists:property_values,name,property_id,'
                . Property::where('name', 'grade')->firstOrFail()->id,
            'class' => 'nullable|exists:property_values,name,property_id,'
                . Property::where('name', 'class')->firstOrFail()->id,
            'political_status' => 'nullable|exists:property_values,name,property_id,'
                . Property::where('name', 'political_status')->firstOrFail()->id,
            'native_place.*' => 'nullable|exists:property_values,name,property_id,'
                . Property::where('name', 'native_place')->firstOrFail()->id,
            'financial_difficulty' => 'nullable|exists:property_values,name,property_id,'
                . Property::where('name', 'financial_difficulty')->firstOrFail()->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::FORM_ILLEGAL,
                'errmsg' => $validator->errors()->first(),
            ]);
        }

        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->save();

        $nativePlace = $request->input('native_place');
        while (!empty($nativePlace) && !end($nativePlace)) {
            array_pop($nativePlace);
        }
        $user->setProperty('grade', $request->input('grade'))
            ->setProperty('class', $request->input('class'))
            ->setProperty('political_status', $request->input('political_status'))
            ->setProperty('native_place', end($nativePlace))
            ->setProperty('financial_difficulty', $request->input('financial_difficulty'));

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
                'important' => $item->important,

                'read' => (boolean)($read_at = $item->pivot->read_at),
                'read_at' => $read_at ? strtotime($read_at) : null,

                'star' => (boolean)($stared_at = $item->pivot->stared_at),
                'stared_at' => $stared_at ? strtotime($stared_at) : null,

                'delete' => (boolean)($deleted_at = $item->pivot->deleted_at),
                'deleted_at' => $deleted_at ? strtotime($deleted_at) : null,

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
        $department = $notification->department;
        return response()->json([
            'errcode' => ErrCode::OK,
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'author' => $notification->user->name,
                'department' => $department->number,
                'department_name' => $department->name,
                'department_avatar' => url($department->avatar),
                'start_time' => $notification->start_time->timestamp,
                'end_time' => $notification->end_time->timestamp,
                'content' => $notification->content,
                'files' => $notification->files->map(function ($item, $key) {
                    return $item->downloadInfo();
                }),
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
        if (!$notification = $user->receivedNotifications()->find($id)) {
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
        $pivot->read_at = Carbon::now();
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
        $pivot->stared_at = Carbon::now();
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
        $pivot->stared_at = null;
        $pivot->save();
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Unstared!',
        ]);
    }
}