<?php

namespace Lazycode\Permissions;

use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish the configuration and migration files
        $this->publishes([
            __DIR__ . '\config\permissions.php' => config_path('permissions.php'), // Note the correct file name
            __DIR__ . '\database\migrations\create_permission_tables.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permission_tables.php'),
        ], 'config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'\database\migrations');
    }

    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(__DIR__ . '\config\permissions.php', 'permissions'); // Ensure this matches the config file
    }
}
