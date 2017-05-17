<?php

namespace App\Http\Controllers;


use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccountManagerController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['except' => 'getImportTemplate']);
    }

    public function index(Request $request)
    {
        $auth_user = Auth::user();

        if (!in_array($sort = $request->input('sort'), ['department_id', 'number', 'name', 'role_id'], true))
            $sort = 'number';
        if (!in_array($by = $request->input('by'), ['asc', 'desc'], true))
            $by = 'asc';

        $wd = null;
        if ($auth_user->can('view_all_user')) {
            if ($request->has('wd')) {
                $wd = $request->input('wd');
                $query_wd = '%' . str_replace("_", "\\_", str_replace("%", "\\%", $wd)) . '%';
                $users = User::where('number', 'like', $query_wd)
                    ->orWhere('name', 'like', $query_wd)
                    ->orderBy($sort, $by)
                    ->paginate(25);
            } else {
                $users = User::orderBy($sort, $by)->paginate(25);
            }
        } else if ($auth_user->can('view_owned_user')) {
            if ($request->has('wd')) {
                $wd = $request->input('wd');
                $query_wd = '%' . str_replace("_", "\\_", str_replace("%", "\\%", $wd)) . '%';
                $users = $auth_user->department->users()
                    ->where('number', 'like', $query_wd)
                    ->orWhere('name', 'like', $query_wd)
                    ->orderBy($sort, $by)
                    ->paginate(25);
            } else {
                $users = $auth_user->department->users()
                    ->orderBy($sort, $by)
                    ->paginate(25);
            }
        } else
            throw new AccessDeniedHttpException();

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $users->lastPage()))
                return redirect($users->url($lastPage));
            if ($page < 1)
                return redirect($users->url(1));
        }

        return view('accountManager.index', [
            'users' => $users->appends(['wd' => $wd, 'sort' => $sort, 'by' => $by]),
            'wd' => $wd,
            'sort' => $sort,
            'by' => $by,
        ]);
    }

    public function show($id)
    {
        $auth_user = Auth::user();

        if ($auth_user->canDo(PrivilegeDef::VIEW_ALL_USER)) {
            $user = User::findOrFail($id);
        } else if ($auth_user->canDo(PrivilegeDef::VIEW_DEPARTMENT_USER)) {
            $user = $auth_user->department->users()->findOrFail($id);
        } else {
            throw new AccessDeniedHttpException();
        }

        if ($auth_user->canDo(PrivilegeDef::MODIFY_USER)
            && (RoleDef::isChild($auth_user->role_id, $user->role_id) || $auth_user->id === $user->id)
        ) {
            return view('accountManager.edit', [
                'user' => $user,
            ]);
        }
        throw new AccessDeniedHttpException();
    }


    public function create()
    {
        $auth_user = Auth::user();

        if ($auth_user->canDo(PrivilegeDef::ADD_USER)) {
            return view('accountManager.create');
        }
        throw new AccessDeniedHttpException();
    }

    public function store(Request $request)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::ADD_USER)) {
            if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
                $this->validate($request, [
                    'number' => 'required|digits_between:4,8|unique:users,number',
                    'name' => 'required',
                    'department' => 'required|exists:departments,id',
                    'email' => 'email|unique:users,email',
                    'phone' => 'phone|unique:users,phone',
                    'role' => 'required|role:' . $auth_user->role_id,
                ]);
            } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
                $this->validate($request, [
                    'number' => 'required|digits_between:4,8|unique:users,number',
                    'name' => 'required',
                    'department' => 'required|in:' . $auth_user->department_id,
                    'email' => 'email|unique:users,email',
                    'phone' => 'phone|unique:users,phone',
                    'role' => 'required|role:' . $auth_user->role_id,
                ]);
            } else
                throw new AccessDeniedHttpException();

            $user = User::create([
                'number' => $request->input('number'),
                'name' => $request->input('name'),
                'department_id' => $request->input('department'),
                'email' => $request->has('email') ? $request->input('email') : null,
                'phone' => $request->has('phone') ? $request->input('phone') : null,
                'role_id' => $request->input('role'),
            ]);
            return redirect('/account_manager/' . $user->id);
        }
        throw new AccessDeniedHttpException();
    }

    public function update(Request $request, $id)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::VIEW_ALL_USER)) {
            $user = User::findOrFail($id);
            $this->validate($request, [
                'name' => 'required',
                'department' => 'required|exists:departments,id',
                'email' => 'email|unique:users,email,' . $user->id,
                'phone' => 'digits:11|unique:users,phone,' . $user->id,
                'role' => 'required|role:' . $auth_user->role_id . ','
                    . (string)($auth_user->id === $user->id),
            ]);
        } else if ($auth_user->canDo(PrivilegeDef::VIEW_DEPARTMENT_USER)) {
            $user = $auth_user->department->users()->findOrFail($id);
            $this->validate($request, [
                'name' => 'required',
                'department' => 'required|in:' . $auth_user->department_id,
                'email' => 'email|unique:users,email,' . $user->id,
                'phone' => 'digits:11|unique:users,phone,' . $user->id,
                'role' => 'required|role:' . $auth_user->role_id . ','
                    . (string)($auth_user->id === $user->id),
            ]);
        } else
            throw new AccessDeniedHttpException();

        if ($auth_user->canDo(PrivilegeDef::MODIFY_USER)
            && (RoleDef::isChild($auth_user->role_id, $user->role_id) || $auth_user->id === $user->id)
        ) {
            $user->name = $request->input('name');
            $user->department_id = $request->input('department');
            $user->email = $request->has('email') ? $request->input('email') : null;
            $user->phone = $request->has('phone') ? $request->input('phone') : null;
            $user->role_id = $request->input('role');
            if ($request->has('clear_password') && $request->input('clear_password') === "on") {
                $user->updatePassword(null);
            }
            $user->save();
            return redirect('/account_manager/' . $user->id);
        }
        throw new AccessDeniedHttpException();
    }

    public function destroy($id)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::VIEW_ALL_USER))
            $user = User::findOrFail($id);
        else if ($auth_user->canDo(PrivilegeDef::VIEW_DEPARTMENT_USER))
            $user = $auth_user->department->users()->findOrFail($id);
        else
            throw new AccessDeniedHttpException();

        if ($auth_user->canDo(PrivilegeDef::DELETE_USER)
            && (RoleDef::isChild($auth_user->role_id, $user->role_id) || $auth_user->id === $user->id)
        ) {
            $user->delete();
            return response('成功删除！');
        }
        throw new AccessDeniedHttpException();
    }

    public function getImportTemplate()
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->fromArray(['学号／工号', '姓名', '院系', '邮箱', '手机号'])
            ->setTitle('Account');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="账号导入模板.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function import(Request $request)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::ADD_USER)
            && $request->hasFile('file')
            && ($file = $request->file('file'))->isValid()
        ) {
            $file->move($dirName = pathinfo($file->getRealPath())['dirname'], $fileName = $file->getClientOriginalName());
            $path = $dirName . '/' . $fileName;
            $objPHPExcel = PHPExcel_IOFactory::load($path);
            $sheetData = $objPHPExcel->setActiveSheetIndex()->toArray('', true, true, true);
            unlink($path);

            $success = 0;
            $skip = 0;
            $fail = 0;
            foreach ($sheetData as $key => $value) {
                if ($key === 1 || empty(current($value)))
                    continue;

                $validator = Validator::make($value, [
                    'A' => 'unique:users,number', //user_id
                ]);
                if ($validator->fails()) {
                    $skip++;
                    continue;
                }

                $validator = Validator::make($value, [
                    'A' => 'required|digits_between:4,8', //user_id
                    'B' => 'required', //name
                    'C' => 'required|exists:departments,number', //department
                    'D' => 'email|unique:users,email', //email
                    'E' => 'phone|unique:users,phone', //phone
                ]);
                if ($validator->fails()) {
                    $fail++;
                    continue;
                }

                User::create([
                    'number' => $value['A'],
                    'name' => $value['B'],
                    'department_id' => Department::where('number', $value['C'])->firstOrFail()->id,
                    'email' => empty($value['D']) ? null : $value['D'],
                    'phone' => empty($value['E']) ? null : $value['E'],
                    'role_id' => RoleDef::NORMAL['id'],
                ]);
                $success++;
            }

            return response()->json([
                'success' => $success,
                'skip' => $skip,
                'fail' => $fail,
            ]);
        }
        return response()->json(['errmsg' => '上传错误']);
    }
}