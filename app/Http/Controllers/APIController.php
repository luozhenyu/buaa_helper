<?php

namespace App\Http\Controllers;

use App\Func\ErrCode;
use App\Jobs\SendText;
use App\Models\City;
use App\Models\Device;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class APIController extends Controller
{
    /**
     * APIController constructor.
     * Need to pass api auth.
     */
    function __construct()
    {
        $this->middleware('auth.jwt', ['except' => ['index', 'login', 'JWTLogin', 'JWTRefresh']]);
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
        if (!Auth::attempt($this->credentials($request))) {
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

    public function JWTLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'errcode' => ErrCode::CREDENTIALS_ERROR,
                    'errmsg' => Lang::get('auth.failed'),
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'errcode' => ErrCode::SERVER_ERROR,
                'errmsg' => 'could_not_create_token',
            ]);
        }
        return response()->json([
            'errcode' => ErrCode::OK,
            'token' => $token,
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

    public function JWTRefresh(Request $request)
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            return response()->json([
                'errcode' => ErrCode::CREDENTIALS_ERROR,
                'errmsg' => 'token_expired',
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'errcode' => ErrCode::CREDENTIALS_ERROR,
                'errmsg' => 'token_invalid',
            ]);
        }
        return response()->json([
            'errcode' => ErrCode::OK,
            'token' => $newToken,
        ]);
    }


    public function listDevice(Request $request)
    {
        $devices = Auth::user()->devices->map(function ($item, $key) {
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
        $device->user()->associate(Auth::user());
        $device->touch();
        return response()->json([
            'errcode' => ErrCode::OK,
            'msg' => 'Created!',
        ]);
    }

    public function deleteDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|exists:devices,registrationID,user_id,' . Auth::user()->id,
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
        $validator = Validator::make($request->all(), [
            'text' => 'required|max:70',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::FORM_ILLEGAL,
                'errmsg' => $validator->errors()->first(),
            ]);
        }
        dispatch(new SendText($request->input('text'), Auth::user()));

        return response()->json([
            'errcode' => ErrCode::OK,
        ]);
    }


    public function userInfo(Request $request)
    {
        $user = Auth::user();
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

    public function userParams(Request $request)
    {
        $params = collect(['grade', 'class', 'political_status', 'financial_difficulty']);

        return response()->json($params->map(function ($item, $key) {
            $property = Property::where('name', $item)->firstOrFail();

            return [
                'name' => $property->name,
                'display_name' => $property->display_name,
                'property_values' => $property->propertyValues->map(function ($item, $key) {
                    return [
                        'name' => $item->name,
                        'display_name' => $item->display_name,
                    ];
                }),
            ];
        }));
    }

    public function cities()
    {
        $cities = City::get()->toArray();

        $cityMap = [];
        $cityRef = [];
        foreach ($cities as &$city) {
            $parentID = $city['parent_id'];
            $id = $city['id'];
            $item = [
                'code' => $city['code'],
                'name' => $city['name'],
                'children' => [],
            ];

            if (!$parentID) {
                $cityMap[] = &$item;
            } else {
                $cityRef[$parentID]['children'][] = &$item;
            }
            $cityRef[$id] = &$item;
            unset($item);
        }

        return response()->json($cityMap);
    }

    public function modifyUserAvatar(Request $request)
    {
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
            Image::make($uploadFile)
                ->resize(200, 200)
                ->encode('png')
                ->save();
        } catch (NotReadableException $e) {
            return response()->json([
                "uploaded" => 0,
                "message" => "文件不是图片类型",
            ]);
        }

        $file = FileController::import($uploadFile->getRealPath(), $fileName);

        $user = Auth::user();
        $user->avatarFile()->associate($file);
        $user->save();

        return response()->json([
            'errcode' => ErrCode::OK,
        ]);
    }

    public function modifyUserInfo(Request $request)
    {
        $user = Auth::user();
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
        $user->setProperty('grade', $request->input('grade'));
        $user->setProperty('class', $request->input('class'));
        $user->setProperty('political_status', $request->input('political_status'));
        $user->setProperty('native_place', end($nativePlace));
        $user->setProperty('financial_difficulty', $request->input('financial_difficulty'));

        return response()->json([
            'errcode' => ErrCode::OK,
        ]);
    }

    public function listNotification(Request $request)
    {
        $notifications = Auth::user()->receivedNotifications->map(function ($item, $key) {
            return [
                'id' => $item->id,
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
        if (!$notification = Auth::user()->receivedNotifications()->find($id)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }
        $department = $notification->department;
        return response()->json([
            'errcode' => ErrCode::OK,
            'notification' => [
                'title' => $notification->title,
                'author' => $notification->user->name,
                'department' => $department->number,
                'department_name' => $department->name,
                'department_avatar' => $department->avatarUrl,
                'start_date' => $notification->start_date->timestamp,
                'finish_date' => $notification->finish_date->timestamp,
                'excerpt' => $notification->excerpt,
                'important' => $notification->important,

                'read' => (boolean)($read_at = $notification->pivot->read_at),
                'read_at' => $read_at ? strtotime($read_at) : null,
                'star' => (boolean)($stared_at = $notification->pivot->stared_at),
                'stared_at' => $stared_at ? strtotime($stared_at) : null,
                'delete' => (boolean)($deleted_at = $notification->pivot->deleted_at),
                'deleted_at' => $deleted_at ? strtotime($deleted_at) : null,
            ]
        ]);
    }

    public function showFullNotification(Request $request, $id)
    {
        if (!$notification = Auth::user()->receivedNotifications()->find($id)) {
            return response()->json([
                'errcode' => ErrCode::RESOURCE_NOT_FOUND,
                'errmsg' => Lang::get('errmsg.resource_not_found'),
            ]);
        }

        return response()->json([
            'errcode' => ErrCode::OK,
            'notification' => [
                'content' => $notification->content,
                'files' => $notification->files->map(function ($item, $key) {
                    return $item->downloadInfo;
                }),
            ]
        ]);
    }

    public function deleteNotification(Request $request, $id)
    {
        if (!$notification = Auth::user()->receivedNotifications()->find($id)) {
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
        if (!$notification = Auth::user()->receivedNotifications()->find($id)) {
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
        if (!$notification = Auth::user()->receivedNotifications()->find($id)) {
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
        if (!$notification = Auth::user()->receivedNotifications()->find($id)) {
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
        if (!$notification = Auth::user()->receivedNotifications()->find($id)) {
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