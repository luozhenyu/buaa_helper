<?php

namespace App\Func;

use App\Models\Device;
use Closure;
use Exception;
use JPush\Client as JPush;
use JPush\Exceptions\JPushException;

class Pusher
{
    /**
     * 单次推送最大为1000
     */
    const chunkSize = 800;

    private $pusher;
    private $deviceID;
    private $text;

    public function __construct()
    {
        $app_key = env('JPUSH_APPKEY');
        $master_secret = env('JPUSH_SECRET');
        if (!$app_key || !$master_secret) {
            throw new Exception('JPUSH_APPKEY or JPUSH_SECRET missing.');
        }
        $this->pusher = new JPush($app_key, $master_secret, null);
    }

    /**
     * 添加device
     * @param Device $device
     */
    public function addDevice(Device $device)
    {
        if ($device) {
            $this->deviceID[] = $device->registrationID;
        }
    }

    /**
     * 设置推送文本(最多70个unicode字符)
     * @param string $text
     */
    public function setText(string $text)
    {
        if (mb_strlen($text) > 70) {
            $text = mb_substr($text, 0, 70);
        }
        $this->text = $text;
    }

    /**
     * 发送推送，如果失败，则调用回调函数
     * @param Closure|null $callback
     */
    public function send(Closure $callback = null)
    {
        if (empty($this->deviceID)) {
            return;
        }
        $arrays = array_chunk($this->deviceID, self::chunkSize);
        try {
            foreach ($arrays as $array) {
                $this->pusher->push()
                    ->setPlatform('all')
                    ->addRegistrationId($array)
                    ->setNotificationAlert($this->text)
                    ->send();
            }
        } catch (JPushException $e) {
            if ($callback) {
                $callback($e);
            }
        }
    }
}