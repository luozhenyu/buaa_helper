<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('inquiry.index');
    }

    public function department(Request $request, $department_number)
    {
        $department = Department::where('number', $department_number)->firstOrFail();
        $inquiries = $department->inquiries()->paginate(10);
        return view('inquiry.department', [
            'department' => $department,
            'inquiries' => $inquiries,
        ]);
    }

    public function create(Request $request, $department_number)
    {
        $department = Department::where('number', $department_number)->firstOrFail();

        $this->validate($request, [
            'title' => 'required|min:5|max:40',
            'content' => 'required|min:10|max:65535',
            'secret' => 'nullable|max:65535',
        ]);

        $inquiry = Auth::user()->inquiries()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'secret' => $request->input('secret'),
            'department_id' => $department->id,
        ]);
        return redirect(route('inquiry') . "/{$department->number}/{$inquiry->id}");
    }

    public function show(Request $request, $department_number, $inquiry_id)
    {
        $department = Department::where('number', $department_number)->firstOrFail();

        $inquiry = Inquiry::where([
            'department_id' => $department->id,
            'id' => $inquiry_id,
        ])->firstOrFail();

        $inquiryReplies = $inquiry->inquiryReplies()->orderBy('created_at')->get();

        return view('inquiry.show', [
            'department' => $department,
            'inquiry' => $inquiry,
            'inquiryReplies' => $inquiryReplies,
        ]);
    }

    public function reply(Request $request, $department_number, $inquiry_id)
    {
        $department = Department::where('number', $department_number)->firstOrFail();

        $inquiry = Inquiry::where([
            'department_id' => $department->id,
            'id' => $inquiry_id,
        ])->firstOrFail();

        $authUser = Auth::user();
        abort_unless($authUser->hasPermission('view_all_inquiry')
            || ($authUser->hasPermission('view_owned_inquiry') && $authUser->department->id === $inquiry->department->id)
            || $authUser->id === $inquiry->user->id, 403);

        $this->validate($request, [
            'content' => 'required|min:2|max:65535',
        ]);

        $inquiryReply = $inquiry->inquiryReplies()->create([
            'content' => $request->input('content'),
            'user_id' => Auth::user()->id,
        ]);
        return redirect()->back();
    }
}