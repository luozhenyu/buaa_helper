<?php

namespace App\Http\Controllers;


use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zizaco\Entrust\EntrustFacade;

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

        if ($auth_user->can('view_all_user')) {
            $user = User::findOrFail($id);
        } else if ($auth_user->can('view_owned_user')) {
            $user = $auth_user->department->users()->findOrFail($id);
        } else {
            abort(403);
        }
        abort_unless($auth_user->can(['modify_all_user', 'modify_owned_user']), 403);

        return view('accountManager.edit', [
            'user' => $user,
        ]);
    }


    public function create()
    {
        abort_unless(EntrustFacade::can('create_user'), 403);
        return view('accountManager.create');
    }

    public function store(Request $request)
    {
        abort_unless(EntrustFacade::can('create_user'), 403);

        $this->validate($request, [
            'number' => 'required|digits_between:4,8|unique:users,number',
            'name' => 'required',
            'department' => 'required|exists:departments,id',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|phone|unique:users,phone',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'number' => $request->input('number'),
            'name' => $request->input('name'),
            'department_id' => $request->input('department'),
            'email' => $request->has('email') ? $request->input('email') : null,
            'phone' => $request->has('phone') ? $request->input('phone') : null,
        ]);

        $role = Role::where('name', $request->input('role'))->firstOrFail();
        $user->attachRole($role);

        return redirect('/account_manager/' . $user->id);
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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['学号／工号', '姓名', '院系', '邮箱', '手机号'])
            ->setTitle('Account');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="账号导入模板.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function import(Request $request)
    {
        $auth_user = Auth::user();
        if ($auth_user->can('create_user')
            && $request->hasFile('file')
            && ($file = $request->file('file'))->isValid()
        ) {
            $file->move($dirName = pathinfo($file->getRealPath())['dirname'], $fileName = $file->getClientOriginalName());
            $path = $dirName . '/' . $fileName;
            $spreadsheet = IOFactory::load($path);
            $sheetData = $spreadsheet->getActiveSheet()
                ->toArray('', true, true, true);
            unlink($path);

            $success = 0;
            $skip = 0;
            $fail = 0;

            $normal = Role::where('name', 'normal')->firstOrFail();

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

                $user = User::create([
                    'number' => $value['A'],
                    'name' => $value['B'],
                    'department_id' => Department::where('number', $value['C'])->firstOrFail()->id,
                    'email' => empty($value['D']) ? null : $value['D'],
                    'phone' => empty($value['E']) ? null : $value['E'],
                ]);
                $user->attachRole($normal);
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