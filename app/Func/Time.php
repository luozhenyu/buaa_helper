<?php

namespace App\Func;

use Carbon\Carbon;
use Exception;

class Time
{
    /**
     * @param Carbon $time
     * @return string
     * @throws \Exception
     */
    public static function format(Carbon $time)
    {
        if ($time->isFuture()) {
            throw new Exception("The time must be a past time");
        }

        $prefix = '';
        if ($time->diffInDays() < 7) {
            if ($time->isToday()) {
                $hour = $time->hour;
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
            } else if ($time->isYesterday()) {
                $prefix = '昨天';
            } else if ($time->isSunday()) {
                $prefix = '星期日';
            } else if ($time->isMonday()) {
                $prefix = '星期一';
            } else if ($time->isTuesday()) {
                $prefix = '星期二';
            } else if ($time->isWednesday()) {
                $prefix = '星期三';
            } else if ($time->isThursday()) {
                $prefix = '星期四';
            } else if ($time->isFriday()) {
                $prefix = '星期五';
            } else if ($time->isSaturday()) {
                $prefix = '星期六';
            }
        } else if ($time->isCurrentYear()) {
            $prefix = $time->format('m月d日');
        } else {
            $prefix = $time->format('Y年m月d日');
        }
        return $prefix . ' ' . $time->format('H:i');
    }
}