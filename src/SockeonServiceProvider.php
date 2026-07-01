<?php

namespace Sockeon\Laravel;

use Illuminate\Support\ServiceProvider;
use Sockeon\Laravel\Console\InstallCommand;
use Sockeon\Laravel\Console\ServeCommand;
use Sockeon\Sockeon\Logging\Logger;
use Sockeon\Sockeon\Logging\LogLevel;

class SockeonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sockeon.php', 'sockeon');

        $this->app->singleton(Logger::class, function () {
            /** @var array{
             *     level?: string|null,
             *     to_console?: bool,
             *     to_file?: bool,
             *     directory?: string|null,
             *     separate_files?: bool
             * } $logging
             */
            $logging = config('sockeon.logging', []);
            $debug = (bool) config('sockeon.debug', false);
            $level = $logging['level'] ?? ($debug ? LogLevel::DEBUG : LogLevel::INFO);

            return new Logger(
                minLogLevel: $level,
                logToConsole: $logging['to_console'] ?? true,
                logToFile: $logging['to_file'] ?? true,
                logDirectory: $logging['directory'] ?? storage_path('logs/sockeon'),
                separateLogFiles: $logging['separate_files'] ?? false,
            );
        });
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
