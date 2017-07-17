<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LuoZhenyu\PostgresFullText\FulltextBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet;

class NotificationController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Auth::user()->receivedNotifications();

        //search
        $wd = null;
        if ($request->has('wd')) {
            $wd = $request->input('wd');
            $fulltext = new FulltextBuilder(['title', 'content']);
            $query = $query->where($fulltext->search($wd));
        }

        //paginate
        $notifications = $query->with('department')->orderBy('published_at', 'desc')->paginate(15);
        if (!is_null($wd)) {
            $notifications = $notifications->appends(['wd' => $wd]);
        }

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $notifications->lastPage()))
                return redirect($notifications->url($lastPage));
            if ($page < 1)
                return redirect($notifications->url(1));
        }

        return view('notification.index', [
            'notifications' => $notifications,
            'wd' => $wd,
        ]);
    }

    public function show(Request $request, $id)
    {
        $notification = Auth::user()->receivedNotifications()->findOrFail($id);

        //普通通知标记为已读
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
        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('create_notification'), 403);

        $this->validate($request, [
            'title' => 'required|max:40',
            'start_date' => 'required|date|after:today',
            'finish_date' => 'required|date|after:start_date',
            'important' => 'required|in:0,1',
            'excerpt' => 'required|max:70',
            'content' => 'required|max:1048576',//2MB
            'attachment.*' => 'size:40|exists:files,hash',
        ]);

        $notification = $authUser->writtenNotifications()->create([
            'title' => $request->input('title'),
            'department_id' => $authUser->department_id,
            'start_date' => $this->ISOStringToCarbon($request->input('start_date')),
            'finish_date' => $this->ISOStringToCarbon($request->input('finish_date')),
            'important' => (bool)$request->input('important'),
            'excerpt' => $request->input('excerpt'),
            'content' => clean($request->input('content')),
        ]);

        if ($request->has('attachment')) {
            $attachment = array_unique($request->input('attachment'));
            foreach ($attachment as $hash) {
                if ($file = File::where('hash', $hash)->first()) {
                    $notification->files()->attach($file);
                }
            }
        }

        $users = User::get()->map(function ($item, $key) {
            return $item->id;
        });
        $notification->notifiedUsers()->sync($users);

        return redirect(route('notification') . "/{$notification->id}/preview");
    }

    protected function ISOStringToCarbon(string $time)
    {
        return Carbon::createFromTimestamp(strtotime($time));
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
            'content' => 'required|max:1048576',//2MB
            'attachment.*' => 'size:40|exists:files,hash',
        ]);

        $authUser = Auth::user();

        $notification->title = $request->input('title');
        $notification->department_id = $authUser->department_id;
        $notification->start_date = $this->ISOStringToCarbon($request->input('start_date'));
        $notification->finish_date = $this->ISOStringToCarbon($request->input('finish_date'));
        $notification->important = (bool)$request->input('important');
        $notification->excerpt = $request->input('excerpt');
        $notification->content = clean($request->input('content'));
        $notification->save();

        $notification->files()->detach();
        if ($request->has('attachment')) {
            $attachment = array_unique($request->input('attachment'));
            foreach ($attachment as $hash) {
                if ($file = File::where('hash', $hash)->first()) {
                    $notification->files()->attach($file);
                }
            }
        }

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
        $notification = Auth::user()->writtenNotifications()->findOrFail($id);

        return response()->json([
            'title' => $notification->title,
            'link' => route('notification') . "/{$notification->id}/statistic",
            'user_read_cnt' => $notification->readUsers->count(),
            'user_not_read_cnt' => ($user_not_read = $notification->notReadUsers)->count(),
            'users' => $user_not_read->take(50)->map(function ($item, $key) {
                return $item->number;
            }),
        ]);
    }

    public function statisticExcel(Request $request, $id)
    {
        $notification = Auth::user()->writtenNotifications()->findOrFail($id);

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
