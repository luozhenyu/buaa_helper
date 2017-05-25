<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'download']);
    }

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

        $fileName = $uploadFile->getClientOriginalName();
        $sha1 = sha1_file($uploadFile->getRealPath());
        if (!$file = File::where('sha1', $sha1)->first()) {
            $path = $uploadFile->storeAs('upload/' . substr($sha1, 0, 2), $sha1);
            $file = Auth::user()->files()->create([
                'sha1' => $sha1,
                'fileName' => $fileName,
                'path' => $path,
            ]);
        }

        return response()->json(
            array_merge(["uploaded" => 1], $this->getArray($file))
        );
    }

    public function download($sha1)
    {
        $file = File::where('sha1', $sha1)->firstOrFail();
        return response()->download(storage_path('app/' . $file->path), $file->fileName);
    }

    public static function getArray($file)
    {
        return [
            "sha1" => $file->sha1,
            "fileName" => $file->fileName,
            "url" => url('/file/download/' . $file->sha1)
        ];
    }
}
