<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Xavrsl\Cas\Facades\Cas;

class TestController extends Controller
{
    public function test(Request $request)
    {
        Cas::authenticate();

        echo Cas::getCurrentUser();

    }
}
