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

    }
}