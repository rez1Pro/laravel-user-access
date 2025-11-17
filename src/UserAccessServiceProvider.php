<?php

namespace Rez1pro\UserAccess;

use Illuminate\Support\ServiceProvider;

class UserAccessServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register console commands only when running in CLI
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Rez1pro\UserAccess\Console\Commands\InstallCommand::class,
                \Rez1pro\UserAccess\Console\Commands\PermissionCreateCommand::class,
                \Rez1pro\UserAccess\Console\Commands\PermissionInsertCommand::class,
                \Rez1pro\UserAccess\Console\Commands\PermissionRollbackCommand::class,
                \Rez1pro\UserAccess\Console\Commands\PermissionFreshCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../stubs/migrations/create_user_access_tables.php.stub' =>
                    database_path('migrations/' . date('Y_m_d_His') . '_create_user_access_tables.php'),
            ], 'user-access-migrations');
        }
    }

    public function register()
    {
        // Bind a singleton the Facade will resolve
        $this->app->singleton('user-access', function ($app) {
            return new \Rez1pro\UserAccess\PermissionEnumManager();
        });
    }
}