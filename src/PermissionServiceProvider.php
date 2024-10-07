<?php

namespace Lazycode\LaravelSimplePermission;

use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish the configuration and migration files
        $this->publishes([
            __DIR__ . '/../config/permission.php' => config_path('permission.php'),
            __DIR__ . '/../database/migrations/create_permission_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permission_tables.php'),
        ], 'config');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/permission.php', 'permission');
    }
}
