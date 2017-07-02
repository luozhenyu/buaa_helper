<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LogController extends Controller
{
    const MAX_LENGTH = 10;

    protected function logStr()
    {
        return 'ios-log';
    }

    protected function getLogArr()
    {
        $logArr = Redis::get($this->logStr()) ?? '[]';
        return json_decode($logArr);
    }

    public function index(Request $request)
    {
        $logArr = $this->getLogArr();
        foreach (array_reverse($logArr) as $log) {
            echo "<p>{$log}</p>";
        }
    }

    public function write(Request $request)
    {
        $log = $request->input('log');

        $logArr = $this->getLogArr();
        $time = Carbon::now()->toDateTimeString();

        array_push($logArr, "[$time] {$log}");
        while (count($logArr) > self::MAX_LENGTH) {
            array_shift($logArr);
        }

        $logArr = json_encode($logArr);
        Redis::set($this->logStr(), $logArr);
        return 'ok';
    }

    public function clear(Request $request)
    {
        Redis::del($this->logStr());
        return 'ok';
    }
}