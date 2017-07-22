<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    const MAX_GROUPS = 10;

    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('view_owned_student'), 403);

        $groups = $authUser->groups()->orderBy('name', 'asc')->get();
        return response()->json([
            'msg' => $groups->count() . '条记录',
            'data' => $groups,
        ]);
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('view_owned_student'), 403);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:10',
                Rule::unique('groups')->where(function ($query) use ($authUser) {
                    $query->where('user_id', $authUser->id);
                })
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errmsg' => $validator->errors()->first('name'),
            ]);
        }

        if ($authUser->groups->count() >= static::MAX_GROUPS) {
            return response()->json([
                'errmsg' => '用户分组数量已达上限。',
            ]);
        }

        $authUser->groups()->create([
            'name' => $request->input('name')
        ]);

        return response()->json([
            'msg' => '创建成功!',
        ]);
    }

    public function update(Request $request, $id)
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('view_owned_student'), 403);

        if (!$group = $authUser->groups()->find($id)) {
            return response()->json([
                'errmsg' => '该用户组不存在！',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:10',
                Rule::unique('groups')->where(function ($query) use ($id, $authUser) {
                    $query->where('user_id', $authUser->id)
                        ->where('id', '!=', $id);
                })
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errmsg' => $validator->errors()->first('name'),
            ]);
        }

        $group->name = $request->input('name');
        $group->save();

        return response()->json([
            'msg' => '修改成功!',
        ]);
    }

    public function delete(Request $request, $id)
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('view_owned_student'), 403);

        if (!$group = $authUser->groups()->find($id)) {
            return response()->json([
                'errmsg' => '该用户组不存在！',
            ]);
        }

        $group->delete();

        return response()->json([
            'msg' => '删除成功!',
        ]);
    }

    public function show(Request $request, $id)
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('view_owned_student'), 403);

        if (!$group = $authUser->groups()->find($id)) {
            return response()->json([
                'errmsg' => '该用户组不存在！',
            ]);
        }

        $users = $group->users()->orderBy('number', 'asc')->paginate(8);

        $paginate = $users->toArray();
        $paginate['name'] = $group->name;
        return response()->json($paginate);
    }

    public function insert(Request $request, $id)
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('view_owned_student'), 403);

        if (!$group = $authUser->groups()->find($id)) {
            return response()->json([
                'errmsg' => '该用户组不存在！',
            ]);
        }

        $numbers = (array)$request->input('number');

        $count = 0;
        $failed = [];

        $query = new Student;
        if (!$authUser->hasPermission('view_all_student')) {
            $query->where('department_id', $authUser->department_id);
        }

        foreach ($numbers as $number) {
            if ($this->isInteger($number)) {
                $q = clone $query;
                if ($user = $q->where('number', $number)->first()) {
                    $group->users()->syncWithoutDetaching($user->id);
                    $count++;
                } else {
                    $failed[] = $number;
                }
            }
        }

        return response()->json([
            'msg' => "成功添加{$count}人" . (count($failed) > 0 ? '，失败名单：' . implode(',', $failed) : ''),
        ]);
    }

    function isInteger($input)
    {
        return ctype_digit(strval($input));
    }

    public function erase(Request $request, $id)
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('view_owned_student'), 403);

        if (!$group = $authUser->groups()->find($id)) {
            return response()->json([
                'errmsg' => '该用户组不存在！',
            ]);
        }

        $numbers = (array)$request->input('number');

        $count = 0;
        $failed = [];

        foreach ($numbers as $number) {

            if ($user = $group->users()->where('number', $number)->first()) {
                $group->users()->detach($user->id);
                $count++;
            } else {
                $failed[] = $number;
            }
        }

        return response()->json([
            'msg' => "成功删除{$count}人" . (count($failed) > 0 ? '，失败名单：' . implode(',', $failed) : ''),
        ]);
    }
}
