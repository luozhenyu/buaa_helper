<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Property;
use Illuminate\Http\Request;

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

    public function department(Request $request)
    {
        return view('inquiry.department');
    }

    public function create(Request $request)
    {
        return;
    }

    public function show(Request $request)
    {
        return view('inquiry.show');
    }

    public function reply(Request $request)
    {
        return;
    }
}