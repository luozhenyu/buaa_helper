<?php

namespace App\Func;


class Time
{
    public static function format($raw_time)
    {
        $strToDay = function ($str) {
            return date('Ymd', strtotime($str));
        };

        $prefix = null;
        if ($raw_time->format('Ymd') > $strToDay('-7 Days')) {
            switch ($raw_time->format('Ymd')) {
                case $strToDay('Today'):
                    $prefix = '今天';
                    break;

                case $strToDay('Yesterday'):
                    $prefix = '昨天';
                    break;

                case $strToDay('-2 Days'):
                    $prefix = '前天';
                    break;

                case $strToDay('Last Sunday'):
                    $prefix = '星期日';
                    break;

                case $strToDay('Last Monday'):
                    $prefix = '星期一';
                    break;

                case $strToDay('Last Tuesday'):
                    $prefix = '星期二';
                    break;

                case $strToDay('Last Wednesday'):
                    $prefix = '星期三';
                    break;

                case $strToDay('Last Thursday'):
                    $prefix = '星期四';
                    break;

                case $strToDay('Last Friday'):
                    $prefix = '星期五';
                    break;

                case $strToDay('Last Saturday'):
                    $prefix = '星期六';
                    break;
            };
        } else if ($raw_time->format('Y') == Date('Y')) {
            $prefix = $raw_time->format('m月d日');
        } else {
            $prefix = $raw_time->format('Y年m月d日');
        }
        return $prefix . $raw_time->format('H:i');
    }

}