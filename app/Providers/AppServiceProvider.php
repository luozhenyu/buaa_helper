<?php

namespace App\Providers;

use App\Func\RoleDef;
use App\Models\User;
use App\Observers\UserObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
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

        Carbon::setLocale(substr(App::getLocale(), 0, 2));

        User::observe(UserObserver::class);
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
