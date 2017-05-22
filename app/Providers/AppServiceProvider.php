<?php

namespace App\Providers;

use App\Func\RoleDef;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\d{8}$/', $value) > 0;
        });

        Validator::extend('uuid', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^\w{8}(-\w{4}){3}-\w{12}$/', $value) > 0;
        });

        Validator::extend('files', function ($attribute, $value, $parameters, $validator) {
            $files = json_decode($value, true);
            if (!is_array($files)) {
                return false;
            }
            foreach ($files as $file) {
                if (!is_array($file)
                    || !array_key_exists('title', $file)
                    || !is_string($file['title'])
                    || !array_key_exists('href', $file)
                    || !is_string($file['href'])
                )
                    return false;
            }
            return true;
        });

        Validator::extend('jsonInArray', function ($attribute, $value, $parameters, $validator) {
            $department_array = json_decode($value, true);
            foreach ($department_array as $department) {
                if (!in_array($department, $parameters)) {
                    return false;
                }
            }
            return true;
        });

        Validator::extend('time_range', function ($attribute, $value, $parameters, $validator) {
            $time = explode(' to ', $value);
            if (count($time) != 2) {
                return false;
            }
            $start_time = strtotime($time[0]);
            $end_time = strtotime($time[1]);
            return $start_time && $end_time && $start_time < $end_time;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
