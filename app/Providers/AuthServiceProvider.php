<?php

namespace App\Providers;

use App\Models\User;
use App\Providers\Auth\MultitypeUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('multitype', function ($app, array $config) {
            $model = $config['model'];
            return new MultitypeUserProvider(new BcryptHasher(), $model);
        });
    }
}
