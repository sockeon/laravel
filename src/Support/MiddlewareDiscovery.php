<?php

namespace Sockeon\Laravel\Support;

use Sockeon\Sockeon\Contracts\Http\HttpMiddleware;
use Sockeon\Sockeon\Contracts\WebSocket\HandshakeMiddleware;
use Sockeon\Sockeon\Contracts\WebSocket\WebsocketMiddleware;

class MiddlewareDiscovery
{
    /**
     * @return array{http: list<class-string>, websocket: list<class-string>, handshake: list<class-string>}
     */
    public function discover(string $path, string $namespace): array
    {
        $discovered = [
            'http' => [],
            'websocket' => [],
            'handshake' => [],
        ];

        if (! is_dir($path)) {
            return $discovered;
        }

        foreach (glob($path.'/*.php') ?: [] as $file) {
            $class = $namespace.'\\'.basename($file, '.php');

            if (! class_exists($class)) {
                continue;
            }

            if (is_subclass_of($class, HttpMiddleware::class)) {
                $discovered['http'][] = $class;
            }

            if (is_subclass_of($class, WebsocketMiddleware::class)) {
                $discovered['websocket'][] = $class;
            }

            if (is_subclass_of($class, HandshakeMiddleware::class)) {
                $discovered['handshake'][] = $class;
            }
        }

        return $discovered;
    }
}
