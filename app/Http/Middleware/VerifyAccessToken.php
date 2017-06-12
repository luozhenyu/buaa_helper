<?php

namespace App\Http\Middleware;

use App\Func\ErrCode;
use App\Models\AccessToken;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VerifyAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public static function handle($request, Closure $next)
    {
        $validator = Validator::make($request->all(), ['access_token' => 'required']);
        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::ACCESS_TOKEN_MISSING,
                'errmsg' => $validator->errors()->first(),
            ]);
        }
        $validator = Validator::make($request->all(), ['access_token' => 'uuid']);
        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::ACCESS_TOKEN_INVALID,
                'errmsg' => $validator->errors()->first(),
            ]);
        }
        $validator = Validator::make($request->all(), ['access_token' => 'exists:access_tokens,access_token']);
        if ($validator->fails()) {
            return response()->json([
                'errcode' => ErrCode::ACCESS_TOKEN_INVALID,
                'errmsg' => $validator->errors()->first(),
            ]);
        }
        $user = AccessToken::where('access_token', $request->input('access_token'))->firstOrFail()->user;
        Auth::login($user);
        return $next($request);
    }
}