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
use Zizaco\Entrust\EntrustFacade;

class AccountManagerController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['except' => 'getImportTemplate']);
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        if (!in_array($sort = $request->input('sort'), ['department_id', 'number', 'name', 'role_id'], true))
            $sort = 'number';
        if (!in_array($by = $request->input('by'), ['asc', 'desc'], true))
            $by = 'asc';

        $wd = null;
        if ($authUser->can('view_all_user')) {
            if ($request->has('wd')) {
                $wd = $request->input('wd');
                $queryWord = '%' . str_replace("_", "\\_", str_replace("%", "\\%", $wd)) . '%';
                $users = User::where('number', 'like', $queryWord)
                    ->orWhere('name', 'like', $queryWord)
                    ->orderBy($sort, $by)
                    ->paginate(25);
            } else {
                $users = User::orderBy($sort, $by)->paginate(25);
            }
        } else if ($authUser->can('view_owned_user')) {
            if ($request->has('wd')) {
                $wd = $request->input('wd');
                $queryWord = '%' . str_replace("_", "\\_", str_replace("%", "\\%", $wd)) . '%';
                $users = $authUser->department->users()
                    ->where('number', 'like', $queryWord)
                    ->orWhere('name', 'like', $queryWord)
                    ->orderBy($sort, $by)
                    ->paginate(25);
            } else {
                $users = $authUser->department->users()
                    ->orderBy($sort, $by)
                    ->paginate(25);
            }
        } else {
            abort(403);
        }

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
        $authUser = Auth::user();

        if ($authUser->can('view_all_user')) {
            $user = User::findOrFail($id);
        } else if ($authUser->can('view_owned_user')) {
            $user = $authUser->department->users()->findOrFail($id);
        } else {
            abort(403);
        }
        abort_unless($authUser->can(['modify_all_user', 'modify_owned_user']), 403);

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
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        $role = Role::where('name', $request->input('role'))->firstOrFail();
        $user->attachRole($role);

        return redirect('/account_manager/' . $user->id);
    }


    public function update(Request $request, $id)
    {
        $authUser = Auth::user();
        if ($authUser->can('modify_all_user')) {
            $user = User::findOrFail($id);
            $this->validate($request, [
                'name' => 'required',
                'department' => 'required|exists:departments,id',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|digits:11|unique:users,phone,' . $user->id,
                'role' => 'required|exists:roles,name',
            ]);
            $user->department_id = $request->input('department');

            $role = Role::where('name', $request->input('role'))->firstOrFail();
            $user->roles()->detach();
            $user->attachRole($role);

            if ($request->input('clear_password') === "on") {
                $user->updatePassword(null);
            }

        } else if ($authUser->can('modify_owned_user')) {
            $user = User::where(['id' => $id, 'department' => $authUser->department_id])->firstOrFail($id);
            $this->validate($request, [
                'name' => 'required',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|digits:11|unique:users,phone,' . $user->id,
            ]);
        } else {
            return abort(403);
        }
        $user->name = $request->input('name');
        $user->email = $request->has('email') ? $request->input('email') : null;
        $user->phone = $request->has('phone') ? $request->input('phone') : null;

        $user->save();
        return redirect('/account_manager/' . $user->id);
    }

    public function destroy($id)
    {
        abort_unless(EntrustFacade::can('delete_user'), 403);

        $user = User::findOrFail($id);
        $user->delete();
        return response('成功删除！');
    }

    public function getImportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['学号／工号', '姓名', '院系', '邮箱', '手机号'])
            ->setTitle('Account');

        $dir = storage_path("app/cache");
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = $dir . '/' . str_random();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($path);
        return response()->download($path, "账号导入模板.xlsx")
            ->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        if (EntrustFacade::can('create_user')
            && $request->hasFile('file')
            && ($file = $request->file('file'))->isValid()
        ) {
            $file->move($dirName = pathinfo($file->getRealPath())['dirname'], $fileName = $file->getClientOriginalName());
            $path = $dirName . '/' . $fileName;
            $spreadsheet = IOFactory::load($path);
            $sheetData = $spreadsheet->getActiveSheet()
                ->toArray('', true, true, true);
            unlink($path);

            list($success, $skip, $fail) = [0, 0, 0];

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