<?php

namespace App\Http\Controllers;

use Closure;
use App\Models\File;
use App\Models\RealFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'download']);
    }

    const UPLOAD_MAX_SIZE = 4 * 1024 * 1024;
    const FILE_MAX_SIZE = 100 * 1024 * 1024;

    /**
     * 大小限制提示
     * @return string
     */
    public static function uploadLimitHit()
    {
        $limit = round(static::UPLOAD_MAX_SIZE / 1024 / 1024, 2);
        return "[大小限制:{$limit}MB]";
    }

    /**
     * 物理存储
     * @param string $source
     * @return \App\Models\RealFile|bool
     */
    protected static function createRealFile(string $source)
    {
        if (!file_exists($source)) {
            return false;
        }

        if (($fileSize = filesize($source)) > static::FILE_MAX_SIZE) {
            return false;
        }

        $sha1 = sha1_file($source);
        if (!$realFile = RealFile::where('sha1', $sha1)->first()) {
            $realFile = RealFile::create([
                'sha1' => $sha1,
                'mime' => mime_content_type($source),
                'size' => $fileSize,
            ]);

            $dest = $realFile->absolutePath;

            $dir = dirname($dest);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if (file_exists($dest)) {
                unlink($dest);
            }
            copy($source, $dest);
        }
        return $realFile;
    }

    /**
     * 导入本地文件
     * @param string $source
     * @param string|null $fileName
     * @return File|bool
     */
    public static function import(string $source, string $fileName = null)
    {
        if (!$realFile = static::createRealFile($source)) {
            return false;
        }
        $user = Auth::user();
        $hash = sha1($user->id . $realFile->sha1 . $fileName);

        if (!$file = $user->files()->where('hash', $hash)->first()) {
            $file = $user->files()->create([
                'hash' => $hash,
                'fileName' => $fileName ?: basename($source),
            ]);
            $file->realFile()->associate($realFile);
            $file->save();
        }
        return $file;
    }

    /**
     * 上传文件
     * @param Request $request
     * @param Closure|null $callback
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, Closure $callback = null)
    {
        $uploadFile = $request->file('upload');

        if (!$uploadFile || !$uploadFile->isValid()) {
            return response()->json([
                'uploaded' => 0,
                'message' => '文件上传失败，原因可能是文件过大',
            ]);
        }

        $size = $uploadFile->getSize();
        if ($size <= 0 || $size > static::UPLOAD_MAX_SIZE) {
            return response()->json([
                'uploaded' => 0,
                'message' => '文件超过最大允许长度' . static::uploadLimitHit(),
            ]);
        }

        $fileName = $uploadFile->getClientOriginalName();
        if (strlen($fileName) > 200) {
            return response()->json([
                'uploaded' => 0,
                'message' => '文件名最多为200字符',
            ]);
        }

        $type = $request->input('type');
        if ($type === 'avatar') {
            try {
                Image::make($uploadFile)
                    ->resize(200, 200)
                    ->encode('png')
                    ->save();
            } catch (NotReadableException $e) {
                return response()->json([
                    'uploaded' => 0,
                    'message' => '文件不是图片类型',
                ]);
            }
        }

        $file = static::import($uploadFile->getRealPath(), $fileName);

        $callback && $callback($file);
        return response()->json(
            array_merge(['uploaded' => 1], $file->downloadInfo)
        );
    }

    /**
     * @param string $hash
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($hash)
    {
        $file = File::where('hash', $hash)->firstOrFail();
        $realFile = $file->realFile;
        unset($file);
        $absolutePath = Storage::url($realFile->relativePath);

        if (str_is('image/*', $realFile->mime)) {
            return response()->file($absolutePath, [
                'Content-Type' => $realFile->mime
            ]);
        }
        return response()->download($absolutePath, $realFile->fileName);
    }
}
