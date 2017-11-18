<?php

namespace Freshbitsweb\UserAgentCollector;

use Illuminate\Support\ServiceProvider;

class UserAgentCollectorServiceProvider extends ServiceProvider
{
    /**
    * Attach middleware
    *
    * @return void
    */
    public function register()
    {
        $httpKernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $httpKernel->pushMiddleware(\Freshbitsweb\UserAgentCollector\Middleware\UserAgentCollector::class);
    }

    /**
    * Specify path to load migrations from
    *
    * @return void
    */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
