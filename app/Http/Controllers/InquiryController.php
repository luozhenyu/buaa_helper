<?php

namespace App\Http\Controllers;

use App\Func\PrivilegeDef;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
//        $auth_user = Auth::user();
//
//        if (!in_array($sort = $request->input('sort'), ['title', 'department_id', 'content', 'updated_at'], true))
//            $sort = 'updated_at';
//        if (!in_array($by = $request->input('by'), ['asc', 'desc'], true))
//            $by = 'desc';
//
//        $wd = null;
//        if ($auth_user->canDo(PrivilegeDef::EDIT_ALL_NOTIFICATION)) {
//            if ($request->has('wd')) {
//                $wd = $request->input('wd');
//                $query_wd = '%' . $wd . '%';
//                $notifications = Notification::where('title', 'like', $query_wd)
//                    ->orderBy($sort, $by)
//                    ->paginate(15);
//            } else {
//                $notifications = Notification::orderBy($sort, $by)->paginate(15);
//            }
//        } else if ($auth_user->canDo(PrivilegeDef::EDIT_PERSONAL_NOTIFICATION)) {
//            if ($request->has('wd')) {
//                $wd = $request->input('wd');
//                $query_wd = '%' . $wd . '%';
//                $notifications = $auth_user->written_notifications()
//                    ->where('title', 'like', $query_wd)
//                    ->orderBy($sort, $by)
//                    ->paginate(15);
//            } else {
//                $notifications = $auth_user->written_notifications()
//                    ->orderBy($sort, $by)
//                    ->paginate(15);
//            }
//        } else {
//            throw new AccessDeniedHttpException();
//        }
//
//        if ($page = intval($request->input('page'))) {
//            if ($page > ($lastPage = $notifications->lastPage())) {
//                return redirect($notifications->url($lastPage));
//            }
//            if ($page < 1) {
//                return redirect($notifications->url(1));
//            }
//        }
//
//        return view('notification.index', [
//            'notifications' => $notifications->appends(['wd' => $wd, 'sort' => $sort, 'by' => $by,]),
//            'wd' => $wd,
//            'sort' => $sort,
//            'by' => $by,
//        ]);

        return view('inquiry.index');
    }

    public function show(Request $request, $inquiry_id)
    {
    }

    public function update(Request $request)
    {

    }
}