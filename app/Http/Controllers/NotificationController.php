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

class NotificationController extends Controller
{
    private $orders = [
        'department_id' => [
            'name' => '部门',
            'by' => 'asc',
        ],
        'important' => [
            'name' => '类别',
            'by' => 'asc',
        ],
        'read' => [
            'name' => '阅读情况',
            'by' => 'asc',
        ],
        'title' => [
            'name' => '标题',
            'by' => 'asc',
        ],
        'updated_at' => [
            'name' => '发布时间',
            'by' => 'desc',
        ],
    ];

    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Auth::user()->receivedNotifications();
        //search
        if ($wd = $request->input('wd')) {
            $query = $query->whereRaw("MATCH(`title`,`excerpt`,`content`) AGAINST (? IN NATURAL LANGUAGE MODE)", $wd);
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
        $notifications = $query->with('department')->paginate(15)
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

        if (!$notification->isPublished()) {
            return view('error', [
                'errmsg' => '该通知还未发布，只允许预览',
                'redirect' => route('notification') . "/{$notification->id}/preview",
            ]);
        }

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

    public function draft(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('create_notification'), 403);
        $notifications = Auth::user()->draftNotifications()->paginate(15);

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $notifications->lastPage())) {
                return redirect($notifications->url($lastPage));
            }
            if ($page < 1) {
                return redirect($notifications->url(1));
            }
        }

        return view('notification.draft', [
            'notifications' => $notifications,
        ]);
    }

    public function published(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('create_notification'), 403);
        $notifications = Auth::user()->publishedNotifications()->paginate(15);

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $notifications->lastPage())) {
                return redirect($notifications->url($lastPage));
            }
            if ($page < 1) {
                return redirect($notifications->url(1));
            }
        }

        return view('notification.published', [
            'notifications' => $notifications,
        ]);
    }


    public function create(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('create_notification'), 403);
        return view('notification.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless(Auth::user()->hasPermission('create_notification'), 403);

        $this->validate($request, [
            'title' => 'required|max:40',
            'start_date' => 'required|date|after:today',
            'finish_date' => 'required|date|after:start_date',
            'important' => 'required|in:0,1',
            'excerpt' => 'required|max:70',
            'content' => 'required|max:1048576',//2MB
            'attachment' => 'nullable',
        ]);


        $fileList = [];
        foreach (explode(',', $request->input('attachment')) as $hash) {
            if ($file = File::where('hash', $hash)->first()) {
                $fileList[] = $file->id;
            }
        }

        $title = $request->input('title');
        $start_date = Carbon::createFromTimestamp(strtotime($request->input('start_date')));
        $finish_date = Carbon::createFromTimestamp(strtotime($request->input('finish_date')));

        $notification = $user->writtenNotifications()->create([
            'title' => $title,
            'department_id' => $user->department_id,
            'start_date' => $start_date,
            'finish_date' => $finish_date,
            'important' => $request->input('important') === "1",
            'excerpt' => $request->input('excerpt'),
            'content' => clean($request->input('content')),
        ]);
        $notification->files()->sync($fileList);

        $users = User::get()->map(function ($item, $key) {
            return $item->id;
        });
        $notification->notifiedUsers()->sync($users);

        return redirect(route('notification') . "/{$notification->id}/preview");
    }

    public function delete(Request $request, $id)
    {
        if (Auth::user()->hasPermission('delete_notification')) {
            $notification = Notification::findOrFail($id);
        } else if (Auth::user()->hasPermission('create_notification')) {
            $notification = Auth::user()->draftNotifications()->findOrFail($id);
        } else {
            return abort(403);
        }
        $notification->delete();
        return response('成功删除！');
    }

    public function preview(Request $request, $id)
    {
        abort_unless(Auth::user()->hasPermission('create_notification'), 403);

        $notification = Auth::user()->writtenNotifications()->findOrFail($id);
        if ($notification->isPublished()) {
            return view('error', [
                'errmsg' => '该通知已发布，不能预览',
                'redirect' => route('notification') . "/{$notification->id}",
            ]);
        }
        return view('notification.preview', [
            'notification' => $notification,
        ]);
    }

    public function publish(Request $request, $id)
    {
        abort_unless(Auth::user()->hasPermission('create_notification'), 403);

        $notification = Auth::user()->writtenNotifications()->findOrFail($id);
        if (!$notification->isPublished()) {
            $notification->published_at = Carbon::now();
            $notification->save();
        }
        return redirect(route('notification') . "/{$notification->id}");
    }

    public function modify(Request $request, $id)
    {
        $notification = Auth::user()->writtenNotifications()->findOrFail($id);
        if ($notification->isPublished()) {
            return view('error', [
                'errmsg' => '该通知已发布，不能修改',
                'redirect' => route('notification') . "/{$notification->id}",
            ]);
        }
        return view('notification.modify', [
            'notification' => $notification,
        ]);
    }

    public function update(Request $request, $id)
    {
        $notification = Auth::user()->writtenNotifications()->findOrFail($id);
        $this->validate($request, [
            'title' => 'required|max:40',
            'start_date' => 'required|date|after:today',
            'finish_date' => 'required|date|after:start_date',
            'important' => 'required|in:0,1',
            'excerpt' => 'required|max:70',
            'content' => 'required|max:1048576',
            'attachment' => 'nullable',
        ]);

        $fileList = [];
        foreach (explode(',', $request->input('attachment')) as $hash) {
            if ($file = File::where('hash', $hash)->first()) {
                $fileList[] = $file->id;
            }
        }

        $title = $request->input('title');
        $start_date = Carbon::createFromTimestamp(strtotime($request->input('start_date')));
        $finish_date = Carbon::createFromTimestamp(strtotime($request->input('finish_date')));

        $user = Auth::user();

        $notification->title = $title;
        $notification->department_id = $user->department_id;
        $notification->start_date = $start_date;
        $notification->finish_date = $finish_date;
        $notification->important = $request->input('important') === "1";
        $notification->excerpt = $request->input('excerpt');
        $notification->content = clean($request->input('content'));
        $notification->save();

        $notification->files()->sync($fileList);

        $users = User::get()->map(function ($item, $key) {
            return $item->id;
        });
        $notification->notifiedUsers()->sync($users);

        return redirect(route('notification') . "/{$notification->id}/preview");
    }

    public function star(Request $request, $id)
    {
        abort_unless($notification = Auth::user()->receivedNotifications()->find($id), 403);
        $pivot = $notification->pivot;
        $pivot->stared_at = Carbon::now();
        $pivot->save();
        return response('Stared!');
    }

    public function unstar(Request $request, $id)
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

    public function read(Request $request, $id)
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
