<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    const ALLOW_EXTS = [
        'jpg', 'png',
        'txt', 'docx'
    ];

    const MAX_SIZE = 2 * 1024 * 1024;

    public function upload(Request $request)
    {
        $uploadFile = $request->file('upload');
        if (!$uploadFile->isValid()) {
            return response()->json([
                "uploaded" => 0,
                "message" => "文件上传失败，原因可能是文件过大",
            ]);
        }
        $size = $uploadFile->getSize();
        if ($size <= 0 || $size > self::MAX_SIZE) {
            return response()->json([
                "uploaded" => 0,
                "message" => "文件超过最大允许长度" . round(self::MAX_SIZE / 1024 / 1024, 2) . 'MB'
            ]);
        }

        $originalName = $uploadFile->getClientOriginalName();
        $sha1 = sha1_file($uploadFile->getRealPath());
        if (!$file = File::where('sha1', $sha1)->first()) {
            $path = $uploadFile->storeAs('upload/' . substr($sha1, 0, 2), $sha1);
            $file = Auth::user()->files()->create([
                'sha1' => $sha1,
                'name' => $originalName,
                'path' => $path,
            ]);
        }

        return $file;

        return response()->json([
            "uploaded" => 1,
            "fileName" => "foo.jpg",
            "url" => "/files/foo.jpg",
        ]);
    }

    public function download(Request $request)
    {

    }
}
