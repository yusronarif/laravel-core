<?php

namespace Yusronarif\Core;

use Illuminate\Support\ServiceProvider;
use Yusronarif\Core\Providers\BladeServiceProvider;
use Yusronarif\Core\Providers\DbServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__.'/../config/yusronarif/core.php';
        $this->mergeConfigFrom($configPath, 'yusronarif.core');

        $this->app->register(DbServiceProvider::class, true);
        $this->app->register(BladeServiceProvider::class, true);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__.'/../config/yusronarif/core.php';
        $this->publishes([$configPath => config_path('yusronarif/core.php')], 'config');
    }
}
