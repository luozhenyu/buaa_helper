<?php

namespace App\Func;

use Carbon\Carbon;
use Exception;

class Time
{
    /**
     * @param Carbon $raw_time
     * @return string
     * @throws \Exception
     */
    public static function format($raw_time)
    {
        if (!$raw_time instanceof Carbon) {
            throw new Exception("The time must be a instance of Carbon");
        }
        if ($raw_time->isFuture()) {
            throw new Exception("The time must be a past time");
        }

        $prefix = '';
        if ($raw_time->diffInDays() < 7) {
            if ($raw_time->isToday()) {
                $hour = $raw_time->hour;
                if ($hour < 6) {
                    $prefix = '凌晨';
                } else if ($hour < 12) {
                    $prefix = '上午';
                } else if ($hour < 14) {
                    $prefix = '中午';
                } else if ($hour < 18) {
                    $prefix = '下午';
                } else {
                    $prefix = '晚上';
                }
            } else if ($raw_time->isYesterday()) {
                $prefix = '昨天';
            } else if ($raw_time->isSunday()) {
                $prefix = '星期日';
            } else if ($raw_time->isMonday()) {
                $prefix = '星期一';
            } else if ($raw_time->isTuesday()) {
                $prefix = '星期二';
            } else if ($raw_time->isWednesday()) {
                $prefix = '星期三';
            } else if ($raw_time->isThursday()) {
                $prefix = '星期四';
            } else if ($raw_time->isFriday()) {
                $prefix = '星期五';
            } else if ($raw_time->isSaturday()) {
                $prefix = '星期六';
            }
        } else if ($raw_time->isCurrentYear()) {
            $prefix = $raw_time->format('m月d日');
        } else {
            $prefix = $raw_time->format('Y年m月d日');
        }
        return $prefix . ' ' . $raw_time->format('H:i');
    }
}