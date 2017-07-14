<?php

namespace App\Http\Controllers;


use App\Models\Admin;
use App\Models\Counsellor;
use App\Models\Department;
use App\Models\DepartmentAdmin;
use App\Models\Property;
use App\Models\Student;
use App\Models\SuperAdmin;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AccountManagerController extends Controller
{
    const userType = [Student::class, Counsellor::class, DepartmentAdmin::class, SuperAdmin::class];

    function __construct()
    {
        $this->middleware('auth', ['except' => 'getImportTemplate']);
    }

    public function index(Request $request)
    {
        $college[] = ['name' => null, 'display_name' => '本年级学生'];

        foreach (Department::where('number', '<', 100)->get() as $department) {
            $college [] = [
                'name' => $department->number,
                'display_name' => $department->display_name,
            ];
        }

        //grade
        $grade = Property::where('name', 'grade')->firstOrFail();

        $students[] = ['name' => ',', 'display_name' => '全校学生'];
        $students = array_merge($students, $grade->propertyValues->map(function ($item, $key) use ($college) {
            $gradeNumber = $item->name;
            return [
                'display_name' => $item->display_name,
                'children' => collect($college)->map(function ($item, $key) use ($gradeNumber) {
                    return [
                        'name' => "{$item['name']},{$gradeNumber}",
                        'display_name' => $item['display_name'],
                    ];
                })->toArray(),
            ];
        })->toArray());


        //properties
        $propertyNames = ['political_status', 'financial_difficulty'];
        $properties = [];
        foreach ($propertyNames as $propertyName) {
            $property = Property::where('name', $propertyName)->firstOrFail();
            $propertyValues = $property->propertyValues->map(function ($item, $key) {
                return [
                    'name' => $item->name,
                    'display_name' => $item->display_name,
                ];
            })->toArray();
            $properties[] = [
                'name' => $property->name,
                'display_name' => $property->display_name,
                'children' => $propertyValues,
            ];
        }

        $selectData = [
            'department' => $students,
            'property' => $properties,
        ];
        return view('accountManager.index', ['selectData' => $selectData]);
    }

    public function show(Request $request, $id)
    {
        $authUser = Auth::user();

        $user = User::findAndDowncasting($id);
        if ($user instanceof Student) {
            abort_unless($authUser->hasPermission('modify_all_student')
                || ($authUser->hasPermission('modify_owned_student')
                    && $authUser->department_id === $user->department_id), 403);
        } else if ($user instanceof Admin) {
            abort_unless($authUser->hasPermission('modify_admin'), 403);
        } else {
            return abort(404);
        }
        return view('accountManager.modify', [
            'user' => $user,
        ]);
    }

    public function ajaxIndex(Request $request)
    {
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission(['view_admin', 'view_all_student', 'view_owned_student']), 403);

        if (!$request->isJson()) {
            return response()->json([
                'errmsg' => '您的请求不是有效的JSON.',
            ]);
        }
        try {
            $condition = $request->toArray();

            if (!key_exists('type', $condition)) {
                throw new Exception('type键不存在');
            }
            if ($condition['type'] === 'admin') {
                $query = Admin::select($condition, $authUser);
            } else if ($condition['type'] === 'student') {
                $query = Student::select($condition, $authUser);
            } else {
                throw new Exception('type只能为student或admin');
            }
        } catch (Exception $e) {
            return response()->json([
                'errmsg' => $e->getMessage(),
            ]);
        }

        return $query->orderBy('number', 'asc')->paginate(15);
    }

    public function create(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('create_user'), 403);
        return view('accountManager.create', [
            'userType' => static::userType,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('create_user'), 403);

        $userType = [];
        foreach (static::userType as $type) {
            $userType[] = (new $type)->role->name;
        }

        $this->validate($request, [
            'number' => 'required|digits_between:4,8|unique:users,number',
            'name' => 'required|max:20',
            'department' => 'required|exists:departments,number',
            'role' => [
                'required',
                Rule::in($userType),
            ],
        ]);

        $role = 'App\\Models\\' . $request->input('role');
        $department = Department::where('number', $request->input('department'))->firstOrFail();

        $user = $role::create([
            'number' => $request->input('number'),
            'name' => $request->input('name'),
            'department_id' => $department->id,
        ]);

        return redirect('/account_manager/' . $user->id);
    }


    public function update(Request $request, $id)
    {
        $authUser = Auth::user();

        $user = User::findAndDowncasting($id);
        if ($user instanceof Student) {
            abort_unless($authUser->hasPermission('modify_all_student')
                || ($authUser->hasPermission('modify_owned_student')
                    && $authUser->department_id === $user->department_id), 403);

            $this->validate($request, [
                'name' => 'required|max:20',
                'department' => 'required|exists:departments,number',
                'grade' => 'nullable|exists:property_values,name,property_id,'
                    . Property::where('name', 'grade')->firstOrFail()->id,
                'class' => 'nullable|exists:property_values,name,property_id,'
                    . Property::where('name', 'class')->firstOrFail()->id,
                'political_status' => 'nullable|exists:property_values,name,property_id,'
                    . Property::where('name', 'political_status')->firstOrFail()->id,
                'native_place' => 'nullable|exists:property_values,name,property_id,'
                    . Property::where('name', 'native_place')->firstOrFail()->id,
                'financial_difficulty' => 'nullable|exists:property_values,name,property_id,'
                    . Property::where('name', 'financial_difficulty')->firstOrFail()->id,
            ]);
            $user->grade = $request->input('grade');
            $user->class = $request->input('class');
            $user->political_status = $request->input('political_status');
            $user->native_place = $request->input('native_place');
            $user->financial_difficulty = $request->input('financial_difficulty');

        } else if ($user instanceof Admin) {
            abort_unless($authUser->hasPermission('modify_admin'), 403);

            $this->validate($request, [
                'name' => 'required|max:20',
                'department' => 'required|exists:departments,number',
            ]);
        } else {
            return abort(404);
        }

        if ($authUser->hasPermission(['modify_all_student', 'modify_admin'])) {
            $department = Department::where('number', $request->input('department'))->firstOrFail();
            $user->department()->associate($department);
        }

        if ($authUser->hasPermission('delete_user') && $request->input('clear_password') === "on") {
            $user->password = null;
        }
        $user->name = $request->input('name');
        $user->save();

        return redirect()->back();
    }

    public function destroy(Request $request, $id)
    {
        abort_unless(Auth::user()->hasPermission('delete_user'), 403);

        $user = User::findAndDowncasting($id);
        $user->delete();
        return response('成功删除！');
    }

    public function getImportTemplate(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['学号／工号', '姓名', '院系号', '邮箱', '手机号'])
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
        if (Auth::user()->hasPermission('create_user')
            && $request->hasFile('file')
            && ($file = $request->file('file'))->isValid()
        ) {
            $file->move($dirName = pathinfo($file->getRealPath())['dirname'], $fileName = $file->getClientOriginalName());
            $path = $dirName . '/' . $fileName;
            try {
                $spreadsheet = IOFactory::load($path);
                $sheetData = $spreadsheet->getActiveSheet()
                    ->toArray(null, true, true, true);
            } catch (\Exception $e) {
                return response()->json(['errmsg' => '文件格式错误']);
            } finally {
                unlink($path);
            }

            list($success, $skip, $fail) = [0, 0, 0];

            $normal = Role::where('name', 'normal')->firstOrFail();
            $msg = [];
            foreach ($sheetData as $key => $value) {
                if (empty(reset($value)) || $key === 1)
                    continue;
                $value['A'] = intval($value['A']);

                $validator = Validator::make($value, [
                    'A' => 'required|unique:users,number', //user_id
                ]);
                if ($validator->fails()) {
                    $msg[] = "跳过：[{$key}行]用户ID已存在";
                    $skip++;
                    continue;
                }

                $validator = Validator::make($value, [
                    'A' => 'required|digits_between:4,8', //user_id
                    'B' => 'required|max:20', //name
                    'C' => 'required|exists:departments,number', //department
                    'D' => 'nullable|email|max:40|unique:users,email', //email
                    'E' => 'nullable|phone|unique:users,phone', //phone
                ]);
                if ($validator->fails()) {
                    $msg[] = "失败：[{$key}行]用户格式错误";
                    $fail++;
                    continue;
                }

                $user = Department::where('number', $value['C'])->firstOrFail()->users()->create([
                    'number' => $value['A'],
                    'name' => $value['B'],
                    'email' => $value['D'],
                    'phone' => $value['E'],
                ]);

                $user->attachRole($normal);
                $success++;
            }

            return response()->json([
                'success' => $success,
                'skip' => $skip,
                'fail' => $fail,
                'msg' => array_slice($msg, 0, 10),
            ]);
        }
        return response()->json(['errmsg' => '上传错误']);
    }
}