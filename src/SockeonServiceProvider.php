<?php

namespace Sockeon\Laravel;

use Illuminate\Support\ServiceProvider;
use Sockeon\Laravel\Console\InstallCommand;
use Sockeon\Laravel\Console\ServeCommand;
use Sockeon\Laravel\Logging\LaravelLogger;

class SockeonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sockeon.php', 'sockeon');

        $this->app->singleton(LaravelLogger::class, fn ($app) => new LaravelLogger($app['log']->driver()));
        $this->app->singleton(SockeonManager::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                ServeCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sockeon.php' => config_path('sockeon.php'),
            ], 'sockeon-config');
        }
    }
}
