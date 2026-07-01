<?php

namespace Sockeon\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Sockeon\Laravel\Logging\LaravelLogger;
use Sockeon\Laravel\Support\ControllerDiscovery;
use Sockeon\Sockeon\Config\ServerConfig;
use Sockeon\Sockeon\Connection\Server;
use Sockeon\Sockeon\Controllers\SocketController;

class SockeonManager
{
    private ?Server $server = null;

    public function __construct(private Application $app) {}

    public function server(): Server
    {
        if ($this->server !== null) {
            return $this->server;
        }

        $config = config('sockeon', []);
        $serverConfig = new ServerConfig($this->serverConfigArray($config));
        $this->server = new Server($serverConfig);
        $this->registerControllers($this->server);

        return $this->server;
    }

    public function serve(): void
    {
        $this->server()->run();
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function serverConfigArray(array $config): array
    {
        $config = Arr::except($config, [
            'controllers',
            'auto_discover',
            'controllers_path',
            'controllers_namespace',
        ]);
        $config['logger'] = $this->app->make(LaravelLogger::class);

        return $config;
    }

    private function registerControllers(Server $server): void
    {
        foreach ($this->resolveControllerClasses() as $class) {
            /** @var SocketController $controller */
            $controller = $this->app->make($class);
            $server->registerController($controller);
        }
    }

    /**
     * @return list<class-string<SocketController>>
     */
    private function resolveControllerClasses(): array
    {
        $config = config('sockeon', []);
        /** @var list<class-string<SocketController>> $classes */
        $classes = $config['controllers'] ?? [];

        if ($config['auto_discover'] ?? false) {
            $discovered = $this->app->make(ControllerDiscovery::class)->discover(
                $config['controllers_path'],
                $config['controllers_namespace'],
            );
            $classes = array_values(array_unique([...$classes, ...$discovered]));
        }

        return $classes;
    }
}
