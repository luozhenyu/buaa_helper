<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'download']);
    }

    const MAX_SIZE = 4 * 1024 * 1024;

    public static function getLimit()
    {
        return '[大小限制:' . round(self::MAX_SIZE / 1024 / 1024, 2) . 'MB]';
    }

    public function upload(Request $request)
    {
        $uploadFile = $request->file('upload');
        $type = $request->input('type');

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
                "message" => "文件超过最大允许长度" . round(self::MAX_SIZE / 1024 / 1024, 2) . 'MB',
            ]);
        }

        $fileName = $uploadFile->getClientOriginalName();
        if (strlen($fileName) > 200) {
            return response()->json([
                "uploaded" => 0,
                "message" => "文件名最多为200字符",
            ]);
        }

        switch ($type) {
            case 'avatar':
                try {
                    $img = Image::make($uploadFile)
                        ->encode('png')
                        ->resize(200, 200)
                        ->save();
                    $mime = $img->mime();
                } catch (NotReadableException $e) {
                    return response()->json([
                        "uploaded" => 0,
                        "message" => "文件不是图片类型",
                    ]);
                }
                break;
            case 'image':
                try {
                    $mime = Image::make($uploadFile)->mime();
                } catch (NotReadableException $e) {
                    return response()->json([
                        "uploaded" => 0,
                        "message" => "文件不是图片类型",
                    ]);
                }
                break;
            default:
                $mime = null;
                break;
        }

        $sha1 = sha1_file($uploadFile->getRealPath());
        if (!$file = File::where('sha1', $sha1)->first()) {
            $path = $uploadFile->storeAs('upload/' . substr($sha1, 0, 2), $sha1);
            $file = Auth::user()->files()->create([
                'sha1' => $sha1,
                'fileName' => $fileName,
                'mime' => $mime,
                'path' => $path,
            ]);
        }

        return response()->json(
            array_merge(["uploaded" => 1], $file->downloadInfo())
        );
    }

    /**
     * @param string $sha1
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($sha1)
    {
        $file = File::where('sha1', $sha1)->firstOrFail();
        switch ($file->mime) {
            case 'image/png':
            case 'image/gif':
            case 'image/jpeg':
            case 'image/bmp':
                return response()->file(storage_path('app/' . $file->path), [
                    'Content-Type' => $file->mime
                ]);
            default:
                return response()->download(storage_path('app/' . $file->path), $file->fileName);
        }
    }
}
