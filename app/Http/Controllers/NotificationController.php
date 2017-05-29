<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet;
use Zizaco\Entrust\EntrustFacade;

class NotificationController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    private $orders = [
        'title' => [
            'name' => '标题',
            'by' => 'asc',
        ],
        'department_id' => [
            'name' => '发布部门',
            'by' => 'asc',
        ],
        'content' => [
            'name' => '正文',
            'by' => 'asc',
        ],
        'updated_at' => [
            'name' => '更新时间',
            'by' => 'desc',
        ],
    ];

    public function index(Request $request)
    {
        $query = Auth::user()->receivedNotifications();
        //search
        if ($wd = $request->input('wd')) {
            $query = $query->whereRaw("MATCH(`title`,`content`) AGAINST (? IN NATURAL LANGUAGE MODE)", $wd);
        }
        //orderBy
        $sort = $request->input('sort');
        $by = $request->input('by');
        if (!$wd || $sort || $by) {
            if (!array_key_exists($sort, $this->orders)) {
                $sort = 'updated_at';//默认updated_at
            }
            if (!in_array($by, ['asc', 'desc'])) {
                $by = $this->orders[$sort]['by'];
            }
            $this->orders[$sort]['by'] = $by === 'asc' ? 'desc' : 'asc';
            $query = $query->orderBy($sort, $by);
        }

        //paginate
        $notifications = $query->paginate(15)
            ->appends(['wd' => $wd, 'sort' => $sort, 'by' => $by]);

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $notifications->lastPage()))
                return redirect($notifications->url($lastPage));
            if ($page < 1)
                return redirect($notifications->url(1));
        }

        return view('notification.index', [
            'notifications' => $notifications,
            'wd' => $wd,
            'orders' => $this->orders,
        ]);
    }

    public function show($id)
    {
        $notification = Auth::user()->receivedNotifications()->findOrFail($id);
        $pivot = $notification->pivot;
        if (!$notification->important) {
            $pivot->read_at = Carbon::now();
            $pivot->save();
        }

        return view('notification.show', [
            'notification' => $notification,
            'stared_at' => $pivot->stared_at,
            'read_at' => $pivot->read_at,
        ]);
    }

    public function manage(Request $request)
    {
        if (EntrustFacade::can('modify_all_notification')) {
            $query = new Notification;
        } else if (EntrustFacade::can('modify_owned_notification')) {
            $query = Auth::user()->written_notifications();
        } else {
            return abort(403);
        }

        //search
        if ($wd = $request->input('wd')) {
            $query = $query->whereRaw("MATCH(`title`,`content`) AGAINST (? IN NATURAL LANGUAGE MODE)", $wd);
        }
        //orderBy
        $sort = $request->input('sort');
        $by = $request->input('by');
        if (!$wd || $sort || $by) {
            if (!array_key_exists($sort, $this->orders)) {
                $sort = 'updated_at';//默认updated_at
            }
            if (!in_array($by, ['asc', 'desc'])) {
                $by = $this->orders[$sort]['by'];
            }
            $this->orders[$sort]['by'] = $by === 'asc' ? 'desc' : 'asc';
            $query = $query->orderBy($sort, $by);
        }

        //paginate
        $notifications = $query->paginate(15)
            ->appends(['wd' => $wd, 'sort' => $sort, 'by' => $by]);

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $notifications->lastPage()))
                return redirect($notifications->url($lastPage));
            if ($page < 1)
                return redirect($notifications->url(1));
        }

        return view('notification.manage', [
            'notifications' => $notifications,
            'wd' => $wd,
            'orders' => $this->orders,
        ]);
    }


    public function create()
    {
        abort_unless(EntrustFacade::can('create_notification'), 403);
        return view('notification.create');
    }

    public function store(Request $request)
    {
        abort_unless(EntrustFacade::can('create_notification'), 403);
        $this->validate($request, [
            'title' => 'required|max:40',
            'department' => 'required|exists:departments,id',
            'time' => 'required|time_range',
            'important' => 'required|in:0,1',
            'content' => 'required|max:1048576',//2MB
            'attachment' => 'nullable',
        ]);

        $time = explode(' ', $request->input('time'));
        $start_time = $time[0] . ' ' . $time[1];
        $end_time = $time[3] . ' ' . $time[4];

        $fileList = [];
        foreach (explode(',', $request->input('attachment')) as $sha1) {
            if ($file = File::where('sha1', $sha1)->first()) {
                $fileList[] = $file->id;
            }
        }

        $user = Auth::user();
        $notification = $user->writtenNotifications()->create([
            'title' => $request->input('title'),
            'department_id' => $user->hasRole('admin') ? $request->input('department') : $user->department_id,
            'start_time' => new Carbon($start_time),
            'end_time' => new Carbon($end_time),
            'important' => $request->input('important') === "1",
            'content' => clean($request->input('content')),
        ]);
        $notification->files()->sync($fileList);

        $users = User::get()->map(function ($item, $key) {
            return $item->id;
        });
        $notification->notifiedUsers()->sync($users);

        return redirect(route('notification') . '/' . $notification->id);
    }

    public function delete($id)
    {
        abort_unless(EntrustFacade::can('delete_notification'), 403);
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return response('成功删除！');
    }


    public function modify($id)
    {
        if (EntrustFacade::can('modify_all_notification')) {
            $notification = Notification::findOrFail($id);
        } else if (EntrustFacade::can('modify_owned_notification')) {
            $notification = Auth::user()->writtenNotifications()->findOrFail($id);
        } else {
            return abort(403);
        }
        return view('notification.modify', [
            'notification' => $notification,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (EntrustFacade::can('modify_all_notification')) {
            $notification = Notification::findOrFail($id);
            $this->validate($request, [
                'title' => 'required|max:40',
                'department' => 'required|exists:departments,id',
                'time' => 'required|time_range',
                'important' => 'required|in:0,1',
                'content' => 'required|max:1048576',
                'attachment' => 'nullable',
            ]);
        } else if (EntrustFacade::can('modify_owned_notification')) {
            $notification = Auth::user()->writtenNotifications()->findOrFail($id);
            $this->validate($request, [
                'title' => 'required|max:40',
                'time' => 'required|time_range',
                'important' => 'required|in:0,1',
                'content' => 'required|max:1048576',
                'attachment' => 'nullable',
            ]);
        } else {
            return abort(403);
        }

        $time = explode(' ', $request->input('time'));
        $start_time = $time[0] . ' ' . $time[1];
        $end_time = $time[3] . ' ' . $time[4];

        $fileList = [];
        foreach (explode(',', $request->input('attachment')) as $sha1) {
            if ($file = File::where('sha1', $sha1)->first()) {
                $fileList[] = $file->id;
            }
        }

        $user = Auth::user();

        $notification->title = $request->input('title');
        $notification->department_id = $user->hasRole('admin') ? $request->input('department') : $user->department_id;
        $notification->start_time = $start_time;
        $notification->end_time = $end_time;
        $notification->important = $request->input('important') === "1";
        $notification->content = clean($request->input('content'));
        $notification->save();

        $notification->files()->sync($fileList);

        $users = User::get()->map(function ($item, $key) {
            return $item->id;
        });
        $notification->notifiedUsers()->sync($users);

        return redirect(route('notification') . '/' . $notification->id);
    }
//
//    public function selectPush($notification_id)
//    {
//        $auth_user = Auth::user();
//        $departments = null;
//
//        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
//            $notification = Notification::findOrFail($notification_id);
//            $departments = Department::get();
//        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
//            $notification = $auth_user->written_notifications()->findOrFail($notification_id);
//            $departments = [$auth_user->department];
//        } else
//            throw new AccessDeniedHttpException();
//
//        $notified_college = [];
//        $notified_department = [];
//
//        foreach ($notification->notified_departments as $department) {
//            if ($department->number < 100)
//                $notified_college[] = $department->id;
//            else
//                $notified_department[] = $department->id;
//        }
//        $notified_users = $notification->notified_users;
//
//        return view('notification.push', [
//            'notification' => $notification,
//            'departments' => $departments,
//            'notified_college' => $notified_college,
//            'notified_department' => $notified_department,
//            'notified_users' => $notified_users,
//        ]);
//    }
//
//    public function push(Request $request, $notification_id)
//    {
//        $auth_user = Auth::user();
//        $department_array = [];
//        $user_array = [];
//        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
//            foreach (Department::get() as $department)
//                $department_array[] = $department->id;
//            foreach (User::get() as $user) {
//                $user_array[] = $user->id;
//            }
//            $notification = Notification::findOrFail($notification_id);
//        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
//            $department_array[] = $auth_user->department_id;
//            $notification = $auth_user->written_notifications()->findOrFail($notification_id);
//            foreach ($auth_user->department->users as $user) {
//                $user_array[] = $user->id;
//            }
//        } else
//            throw new AccessDeniedHttpException();
//
//        $this->validate($request, [
//            'send2college' => 'required|json|json_in_array:' . implode(',', $department_array),
//            'send2department' => 'required|json|json_in_array:' . implode(',', $department_array),
//            'send2user' => 'required|json|json_in_array:' . implode(',', $user_array),
//        ]);
//
//        $send2college = json_decode($request->input('send2college'), true);
//        $send2department = json_decode($request->input('send2department'), true);
//        $send2user = json_decode($request->input('send2user'), true);
//
//        $departments = array_merge($send2college, $send2department);
//        $notification->notified_departments()->sync($departments);
//        $notification->notified_users()->sync($send2user);
//        return redirect(route('notification') . '/' . $notification_id . '/push');
//    }
//
//    public function ajaxSearchUser(Request $request)
//    {
//        $auth_user = Auth::user();
//        $list = $request->input('list');
//        $array = mb_split('\s|,|;', $list);
//
//        $result = [];
//        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
//            foreach ($array as $item) {
//                if (empty($item))
//                    continue;
//                else if ($this->isInteger($item)) {
//                    $number = intval($item);
//                    if (($user = User::where('number', $number)->first()) !== null)
//                        $result[] = [
//                            'id' => $user->id,
//                            'number' => $user->number,
//                        ];
//                }
//            }
//        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
//            foreach ($array as $item) {
//                if (empty($item))
//                    continue;
//                else if ($this->isInteger($item)) {
//                    $number = intval($item);
//                    if (($user = $auth_user->department->users()->where('number', $number)->first()) !== null)
//                        $result[] = [
//                            'id' => $user->id,
//                            'number' => $user->number,
//                        ];
//                }
//            }
//        }
//        $result = $this->unique_multidim_array($result, 'id');
//        sort($result);
//        return response()->json($result);
//    }

    public function star($id)
    {
        abort_unless($notification = Auth::user()->receivedNotifications()->find($id), 403);
        $pivot = $notification->pivot;
        $pivot->stared_at = Carbon::now();
        $pivot->save();
        return response('Stared!');
    }

    public function unstar($id)
    {
        abort_unless($notification = Auth::user()->receivedNotifications()->find($id), 403);
        $pivot = $notification->pivot;
        $pivot->stared_at = null;
        $pivot->save();
        return response('Stared!');
    }

    public function stared(Request $request)
    {
        $notifications = Auth::user()->staredNotifications()->paginate(15);

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $notifications->lastPage())) {
                return redirect($notifications->url($lastPage));
            }
            if ($page < 1) {
                return redirect($notifications->url(1));
            }
        }

        return view('notification.stared', [
            'notifications' => $notifications,
        ]);
    }

    public function read($id)
    {
        $notification = Auth::user()->receivedNotifications()->find($id);
        abort_unless($notification && $notification->important, 403);
        $pivot = $notification->pivot;
        $pivot->read_at = Carbon::now();
        $pivot->save();
        return response('Read!');
    }

    public function statistic(Request $request, $id)
    {
        if (EntrustFacade::can('modify_all_notification')) {
            $notification = Notification::findOrFail($id);
        } else if (EntrustFacade::can('modify_owned_notification')) {
            $notification = Auth::user()->writtenNotifications()->findOrFail($id);
        } else {
            return abort(403);
        }

        return response()->json([
            'title' => $notification->title,
            'link' => route('notification') . '/' . $notification->id . '/statistic',
            'user_read_cnt' => $notification->readUsers->count(),
            'user_not_read_cnt' => ($user_not_read = $notification->notReadUsers)->count(),
            'users' => $user_not_read->take(50)->map(function ($item, $key) {
                return $item->number;
            }),
        ]);
    }

    public function statisticExcel(Request $request, $id)
    {
        if (EntrustFacade::can('modify_all_notification')) {
            $notification = Notification::findOrFail($id);
        } else if (EntrustFacade::can('modify_owned_notification')) {
            $notification = Auth::user()->writtenNotifications()->findOrFail($id);
        } else {
            return abort(403);
        }

        $title = $notification->title;

        $user_read = $notification->readUsers;
        $user_not_read = $notification->notReadUsers;

        $spreadsheet = new Spreadsheet;

        $data = [['学号', '姓名', '手机号']];
        foreach ($user_not_read as $item) {
            $data[] = [$item->number, $item->name, $item->phone];
        }
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data)
            ->setTitle('未读名单');


        $data = [['学号', '姓名', '手机号']];
        foreach ($user_read as $item) {
            $data[] = [$item->number, $item->name, $item->phone];
        }

        $sheet = $spreadsheet->addSheet(new Worksheet());
        $sheet->fromArray($data)
            ->setTitle('已读名单');

        $dir = storage_path("app/cache");
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = $dir . '/' . str_random();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($path);
        return response()->download($path, "{$title} 阅读统计.xlsx")
            ->deleteFileAfterSend(true);
    }
}
