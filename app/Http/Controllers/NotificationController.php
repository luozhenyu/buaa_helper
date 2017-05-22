<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zizaco\Entrust\EntrustFacade;

class NotificationController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public static function insertFile($json_files, $allowDelete = false)
    {
        if (is_null($json_files))
            return null;
        $html = '';
        $URL = url('/ueditor');
        $iconDir = $URL . (substr($URL, strlen($URL) - 1) == '/' ? '' : '/') . 'dialogs/attachment/fileTypeImages/';
        $file_list = json_decode($json_files, true);

        foreach ($file_list as $item) {
            $icon = $iconDir . self::getFileIcon($item['href']);
            $title = $item['title'];
            $html .= '<p style="line-height: 16px;">' .
                '<img style="vertical-align: middle; margin-right: 2px;" src="' . $icon . '" _src="' . $icon . '" />' .
                '<a style="font-size:12px; color:#0066cc;" href="' . $item['href'] . '" title="' . $title . '">' . $title . '</a>'
                . ($allowDelete ? '<span class="glyphicon glyphicon-remove" style="color:red;text-decoration:none;display:inline-block"' .
                    'onclick="var parent=this.parentNode; parent.parentNode.removeChild(parent)"></span>' : ''
                )
                . '</p>';
        }
        return $html;
    }

    private static function getFileIcon($url)
    {
        $ext = strtolower(substr($url, strrpos($url, '.') + 1));
        $maps = [
            "rar" => "icon_rar.gif",
            "zip" => "icon_rar.gif",
            "tar" => "icon_rar.gif",
            "gz" => "icon_rar.gif",
            "bz2" => "icon_rar.gif",
            "doc" => "icon_doc.gif",
            "docx" => "icon_doc.gif",
            "pdf" => "icon_pdf.gif",
            "mp3" => "icon_mp3.gif",
            "xls" => "icon_xls.gif",
            "chm" => "icon_chm.gif",
            "ppt" => "icon_ppt.gif",
            "pptx" => "icon_ppt.gif",
            "avi" => "icon_mv.gif",
            "rmvb" => "icon_mv.gif",
            "wmv" => "icon_mv.gif",
            "flv" => "icon_mv.gif",
            "swf" => "icon_mv.gif",
            "rm" => "icon_mv.gif",
            "exe" => "icon_exe.gif",
            "psd" => "icon_psd.gif",
            "txt" => "icon_txt.gif",
            "jpg" => "icon_jpg.gif",
            "png" => "icon_jpg.gif",
            "jpeg" => "icon_jpg.gif",
            "gif" => "icon_jpg.gif",
            "ico" => "icon_jpg.gif",
            "bmp" => "icon_jpg.gif"
        ];
        return $maps[$ext] ?: $maps['txt'];
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        if (!in_array($sort = $request->input('sort'), ['title', 'department_id', 'content', 'updated_at'], true))
            $sort = 'updated_at';
        if (!in_array($by = $request->input('by'), ['asc', 'desc'], true))
            $by = 'desc';

        $wd = null;
        if ($request->has('wd')) {
            $wd = $request->input('wd');
            $query_wd = '%' . str_replace("_", "\\_", str_replace("%", "\\%", $wd)) . '%';
            $notifications = $authUser->receivedNotifications()
                ->where('title', 'like', $query_wd)
                ->orderBy($sort, $by)
                ->paginate(15);
        } else {
            $notifications = $authUser->receivedNotifications()
                ->orderBy($sort, $by)
                ->paginate(15);
        }

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $notifications->lastPage()))
                return redirect($notifications->url($lastPage));
            if ($page < 1)
                return redirect($notifications->url(1));
        }

        return view('notification.index', [
            'notifications' => $notifications->appends(['wd' => $wd, 'sort' => $sort, 'by' => $by]),
            'wd' => $wd,
            'sort' => $sort,
            'by' => $by,
        ]);
    }

    public function show($id)
    {
        $notification = Auth::user()->receivedNotifications()->findOrFail($id);
        $pivot = $notification->pivot;
        if (!$notification->important) {
            $pivot->read = true;
            $pivot->read_at = Carbon::now();
            $pivot->save();
        }

        return view('notification.show', [
            'notification' => $notification,
            'star' => $pivot->star,
            'read' => $pivot->read,
            'file' => $this->insertFile($notification->files),
        ]);
    }

    public function manage(Request $request)
    {
        $auth_user = Auth::user();

        if (!in_array($sort = $request->input('sort'), ['title', 'department_id', 'content', 'updated_at'], true))
            $sort = 'updated_at';
        if (!in_array($by = $request->input('by'), ['asc', 'desc'], true))
            $by = 'desc';

        $wd = null;
        if ($auth_user->can('modify_all_notification')) {
            if ($request->has('wd')) {
                $wd = $request->input('wd');
                $query_wd = '%' . str_replace("_", "\\_", str_replace("%", "\\%", $wd)) . '%';
                $notifications = Notification::where('title', 'like', $query_wd)
                    ->orderBy($sort, $by)
                    ->paginate(15);
            } else {
                $notifications = Notification::orderBy($sort, $by)->paginate(15);
            }
        } else if ($auth_user->can('modify_owned_notification')) {
            if ($request->has('wd')) {
                $wd = $request->input('wd');
                $query_wd = '%' . str_replace("_", "\\_", str_replace("%", "\\%", $wd)) . '%';
                $notifications = $auth_user->written_notifications()
                    ->where('title', 'like', $query_wd)
                    ->orderBy($sort, $by)
                    ->paginate(15);
            } else {
                $notifications = $auth_user->written_notifications()
                    ->orderBy($sort, $by)
                    ->paginate(15);
            }
        } else {
            abort(403);
        }

        if ($page = intval($request->input('page'))) {
            if ($page > ($lastPage = $notifications->lastPage())) {
                return redirect($notifications->url($lastPage));
            }
            if ($page < 1) {
                return redirect($notifications->url(1));
            }
        }

        return view('notification.manage', [
            'notifications' => $notifications->appends(['wd' => $wd, 'sort' => $sort, 'by' => $by,]),
            'wd' => $wd,
            'sort' => $sort,
            'by' => $by,
        ]);
    }


    public function create()
    {
        abort_unless(EntrustFacade::can('create_notification'), 403);
        return view('notification.create');
    }

    public function store(Request $request)
    {
        dd($request->all());
        $auth_user = Auth::user();

        if ($auth_user->can('modify_all_notification')) {
            $this->validate($request, [
                'title' => 'required',
                'department' => 'required|exists:departments,id',
                'time' => 'nullable|time_range',
                'content' => 'required',
                'files' => 'required|json|files',
            ]);
        } else if ($auth_user->can('modify_owned_notification')) {
            $this->validate($request, [
                'title' => 'required',
                'department' => 'required|in:' . $auth_user->department_id,
                'time' => 'nullable|time_range',
                'content' => 'required',
                'files' => 'required|json|files',
            ]);
        } else
            throw new AccessDeniedHttpException();

        if ($auth_user->can('create_notification')) {

            if ($request->has('time')) {
                $time = explode(' to ', $request->input('time'));
            } else {
                $time = [null, null];
            }

            $notification = Notification::create([
                'title' => $request->input('title'),
                'department_id' => $request->input('department'),
                'user_id' => Auth::user()->id,
                'start_time' => $time[0],
                'end_time' => $time[1],
                'content' => $request->input('content'),
                'files' => $request->input('files'),
                'important' => $request->input('important') === 'on',
            ]);
            return redirect(route('notification') . '/' . $notification->id);
        }

        throw new AccessDeniedHttpException();
    }


    public function edit($notification_id)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
            $notification = Notification::findOrFail($notification_id);
        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
            $notification = $auth_user->written_notifications()->findOrFail($notification_id);
        } else {
            throw new AccessDeniedHttpException();
        }

        return view('notification.edit', [
            'notification' => $notification,
        ]);
    }

    public function update(Request $request, $notification_id)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
            $this->validate($request, [
                'title' => 'required',
                'department' => 'required|exists:departments,id',
                'time' => 'nullable|time_range',
                'content' => 'required',
                'files' => 'required|json|files',
            ]);
        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
            $this->validate($request, [
                'title' => 'required',
                'department' => 'required|in:' . $auth_user->department_id,
                'time' => 'nullable|time_range',
                'content' => 'required',
                'files' => 'required|json|files',
            ]);
        } else
            throw new AccessDeniedHttpException();

        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION))
            $notification = Notification::findOrFail($notification_id);
        else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION))
            $notification = $auth_user->written_notifications()->findOrFail($notification_id);
        else
            throw new AccessDeniedHttpException();

        $notification->title = $request->input('title');
        $notification->department_id = $request->input('department');
        $notification->content = $request->input('content');
        if ($request->has('time')) {
            $time = explode(' to ', $request->input('time'));
            $notification->start_time = $time[0];
            $notification->end_time = $time[1];
        } else {
            $notification->start_time = null;
            $notification->end_time = null;
        }
        $notification->files = $request->input('files');
        $notification->important = $request->input('important') === 'on';
        $notification->save();

        return redirect(route('notification') . '/' . $notification->id);
    }


    public function delete($notification_id)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION))
            $notification = Notification::findOrFail($notification_id);
        else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION))
            $notification = $auth_user->written_notifications()->findOrFail($notification_id);
        else
            throw new AccessDeniedHttpException();

        $notification->delete();
        return response('成功删除！');
    }


    public function selectPush($notification_id)
    {
        $auth_user = Auth::user();
        $departments = null;

        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
            $notification = Notification::findOrFail($notification_id);
            $departments = Department::get();
        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
            $notification = $auth_user->written_notifications()->findOrFail($notification_id);
            $departments = [$auth_user->department];
        } else
            throw new AccessDeniedHttpException();

        $notified_college = [];
        $notified_department = [];

        foreach ($notification->notified_departments as $department) {
            if ($department->number < 100)
                $notified_college[] = $department->id;
            else
                $notified_department[] = $department->id;
        }
        $notified_users = $notification->notified_users;

        return view('notification.push', [
            'notification' => $notification,
            'departments' => $departments,
            'notified_college' => $notified_college,
            'notified_department' => $notified_department,
            'notified_users' => $notified_users,
        ]);
    }

    public function push(Request $request, $notification_id)
    {
        $auth_user = Auth::user();
        $department_array = [];
        $user_array = [];
        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
            foreach (Department::get() as $department)
                $department_array[] = $department->id;
            foreach (User::get() as $user) {
                $user_array[] = $user->id;
            }
            $notification = Notification::findOrFail($notification_id);
        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
            $department_array[] = $auth_user->department_id;
            $notification = $auth_user->written_notifications()->findOrFail($notification_id);
            foreach ($auth_user->department->users as $user) {
                $user_array[] = $user->id;
            }
        } else
            throw new AccessDeniedHttpException();

        $this->validate($request, [
            'send2college' => 'required|json|json_in_array:' . implode(',', $department_array),
            'send2department' => 'required|json|json_in_array:' . implode(',', $department_array),
            'send2user' => 'required|json|json_in_array:' . implode(',', $user_array),
        ]);

        $send2college = json_decode($request->input('send2college'), true);
        $send2department = json_decode($request->input('send2department'), true);
        $send2user = json_decode($request->input('send2user'), true);

        $departments = array_merge($send2college, $send2department);
        $notification->notified_departments()->sync($departments);
        $notification->notified_users()->sync($send2user);
        return redirect(route('notification') . '/' . $notification_id . '/push');
    }


    public function ajaxSearchUser(Request $request)
    {
        $auth_user = Auth::user();
        $list = $request->input('list');
        $array = mb_split('\s|,|;', $list);

        $result = [];
        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
            foreach ($array as $item) {
                if (empty($item))
                    continue;
                else if ($this->isInteger($item)) {
                    $number = intval($item);
                    if (($user = User::where('number', $number)->first()) !== null)
                        $result[] = [
                            'id' => $user->id,
                            'number' => $user->number,
                        ];
                }
            }
        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
            foreach ($array as $item) {
                if (empty($item))
                    continue;
                else if ($this->isInteger($item)) {
                    $number = intval($item);
                    if (($user = $auth_user->department->users()->where('number', $number)->first()) !== null)
                        $result[] = [
                            'id' => $user->id,
                            'number' => $user->number,
                        ];
                }
            }
        }
        $result = $this->unique_multidim_array($result, 'id');
        sort($result);
        return response()->json($result);
    }


    public function star($id)
    {
        abort_unless($notification = Auth::user()->receivedNotifications()->find($id), 403);
        $pivot = $notification->pivot;
        $pivot->star = true;
        $pivot->stared_at = Carbon::now();
        $pivot->save();
        return response('Stared!');
    }

    public function unstar($id)
    {
        abort_unless($notification = Auth::user()->receivedNotifications()->find($id), 403);
        $pivot = $notification->pivot;
        $pivot->star = false;
        $pivot->stared_at = Carbon::now();
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
        $pivot->read = true;
        $pivot->read_at = Carbon::now();
        $pivot->save();
        return response('Read!');
    }

    public function statistic(Request $request, $id)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
            $notification = Notification::findOrFail($id);
        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
            $notification = $auth_user->written_notifications()->findOrFail($id);
        } else
            throw new AccessDeniedHttpException();

        $title = $notification->title;
        $url = route('notification') . '/' . $notification->id . '/statistic';

        $user_all = $notification->notified_all();
        $user_all_cnt = $user_all->count();

        $user_read = $notification->read_users;
        $user_read_cnt = $user_read->count();
        $user_read_percent = $user_read_cnt === 0 ? 0 : round($user_read_cnt / $user_all_cnt * 100, 2);

        $user_not_read = $user_all->diff($user_read);
        $user_not_read_cnt = $user_not_read->count();
        $user_not_read_percent = $user_not_read_cnt === 0 ? 0 : round($user_not_read_cnt / $user_all_cnt * 100, 2);

        return <<<HTML
<h3>$title</h3>
<p>应读人数：{$user_all_cnt}</p>
<p>已读人数：{$user_read_cnt} ({$user_read_percent}%)</p>
<p>未读人数：{$user_not_read_cnt} ({$user_not_read_percent}%)</p>
<a class="btn btn-primary" href="{$url}" target="_blank">统计表下载 [Excel]</a>
HTML;
    }

    public function statisticExcel(Request $request, $id)
    {
        $auth_user = Auth::user();
        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
            $notification = Notification::findOrFail($id);
        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
            $notification = $auth_user->written_notifications()->findOrFail($id);
        } else
            throw new AccessDeniedHttpException();

        $title = $notification->title;

        $user_all = $notification->notified_all();
        $user_read = $notification->read_users;
        $user_not_read = $user_all->diff($user_read);

        $objPHPExcel = new PHPExcel();

        $sheet = [['学号', '姓名', '手机号']];
        foreach ($user_not_read as $item) {
            $sheet[] = [$item->number, $item->name, $item->phone];
        }
        $objPHPExcel->setActiveSheetIndex(0)
            ->fromArray($sheet)
            ->setTitle('未读名单');

        $sheet = [['学号', '姓名', '手机号']];
        foreach ($user_read as $item) {
            $sheet[] = [$item->number, $item->name, $item->phone];
        }
        $objPHPExcel->addSheet(new PHPExcel_Worksheet());
        $objPHPExcel->setActiveSheetIndex(1)
            ->fromArray($sheet)
            ->setTitle('已读名单');

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . ' 阅读统计.xlsx' . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }


    /**
     * Finds whether the given variable is numeric.
     *
     * @param mixed $input
     * @return bool
     */
    protected function isInteger($input)
    {
        return ctype_digit(strval($input));
    }

    public static function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

}
