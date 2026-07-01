<?php

namespace Sockeon\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Sockeon\Laravel\Logging\LaravelLogger;
use Sockeon\Laravel\Support\ControllerDiscovery;
use Sockeon\Laravel\Support\MiddlewareDiscovery;
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
        $this->registerMiddleware($this->server);
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
            'middleware',
        ]);
        $config['logger'] = $this->app->make(LaravelLogger::class);

        return $config;
    }

    private function registerMiddleware(Server $server): void
    {
        /** @var array{
         *     auto_discover?: bool,
         *     path?: string,
         *     namespace?: string,
         *     http?: list<class-string>,
         *     websocket?: list<class-string>,
         *     handshake?: list<class-string>
         * } $middleware
         */
        $middleware = config('sockeon.middleware', []);

        $http = $middleware['http'] ?? [];
        $websocket = $middleware['websocket'] ?? [];
        $handshake = $middleware['handshake'] ?? [];

        if ($middleware['auto_discover'] ?? false) {
            $discovered = $this->app->make(MiddlewareDiscovery::class)->discover(
                $middleware['path'] ?? app_path('Sockeon/Middleware'),
                $middleware['namespace'] ?? 'App\\Sockeon\\Middleware',
            );
            $http = array_values(array_unique([...$http, ...$discovered['http']]));
            $websocket = array_values(array_unique([...$websocket, ...$discovered['websocket']]));
            $handshake = array_values(array_unique([...$handshake, ...$discovered['handshake']]));
        }

        foreach ($http as $class) {
            $server->addHttpMiddleware($class);
        }

        foreach ($websocket as $class) {
            $server->addWebSocketMiddleware($class);
        }

        foreach ($handshake as $class) {
            $server->addHandshakeMiddleware($class);
        }
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
