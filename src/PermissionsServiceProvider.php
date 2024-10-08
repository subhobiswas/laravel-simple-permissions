<?php

namespace Lazycode\Permissions;

use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * This method is called after all other service providers have been registered,
     * allowing you to register things like Blade directives and publish files.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish the configuration and migration files
        $this->publishes([
            __DIR__ . '/config/permissions.php' => config_path('permissions.php'), // Note the correct file name
            __DIR__ . '/database/migrations/create_permission_tables.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permission_tables.php'),
        ], 'config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $bladeCompiler = $this->app->make('blade.compiler');

        $bladeMethodWrapper = function ($method, ...$args) {
            return "<?php if(auth()->user()->{$method}(" . implode(',', array_map(fn($arg) => var_export($arg, true), $args)) . ")): ?>";
        };

        // Role checks
        $bladeCompiler->if('hasRole', fn() => $bladeMethodWrapper('hasRole', ...func_get_args()));
        $bladeCompiler->if('hasAnyRole', fn() => $bladeMethodWrapper('hasAnyRole', ...func_get_args()));

        // Permission checks
        $bladeCompiler->if('hasPermission', fn() => $bladeMethodWrapper('hasPermission', ...func_get_args()));
        $bladeCompiler->if('hasAnyPermission', fn() => $bladeMethodWrapper('hasAnyPermission', ...func_get_args()));
        $bladeCompiler->if('hasAllPermissions', fn() => $bladeMethodWrapper('hasAllPermissions', ...func_get_args()));
        $bladeCompiler->if('hasExactPermissions', fn() => $bladeMethodWrapper('hasExactPermissions', ...func_get_args()));

        // Ending directive for permissions
        $bladeCompiler->directive('endunlesspermission', fn() => '<?php endif; ?>');
    }

    /**
     * Register the application services.
     *
     * This method is called before the application is bootstrapped.
     *
     * @return void
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(__DIR__ . '/config/permissions.php', 'permissions'); // Ensure this matches the config file
    }
}

