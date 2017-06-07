<?php

namespace App\Func;

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
        if (!($app_key = env('JPUSH_APPKEY')) || !($master_secret = env('JPUSH_SECRET'))) {
            throw new \Exception('JPUSH_APPKEY or JPUSH_SECRET missing.');
        }
        $this->pusher = new JPush($app_key, $master_secret, null);
    }

    /**
     * 添加deviceID
     * @param array|string $devices
     */
    public function addRegistrationId($devices)
    {
        foreach ((array)$devices as $device) {
            if (!is_null($device)) {
                $this->deviceID[] = $device;
            }
        }
    }

    /**
     * 设置推送文本(最多70个unicode字符)
     * @param string $text
     */
    public function setText($text)
    {
        $len = mb_strlen($text);
        if ($len > 70) {
            $text = mb_substr($text, 0, 70);
        }
        $this->text = $text;
    }


    public function send(\Closure $callback = null)
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