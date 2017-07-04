<?php

namespace App\Http\Controllers;


class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

}
