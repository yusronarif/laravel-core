<?php

namespace Yusronarif\Core\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/yusronarifCore.php';
        $this->mergeConfigFrom($configPath, 'yusronarifCore');

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
        $configPath = __DIR__ . '/../config/yusronarifCore.php';
        $this->publishes([$configPath => config_path('yusronarifCore.php')], 'config');
    }
}
