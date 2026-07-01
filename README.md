# Sockeon for Laravel

Laravel integration for [Sockeon](https://sockeon.github.io) — a real-time WebSocket and HTTP server for PHP.

This package wires Sockeon into Laravel's service container, config system, logging, and Artisan commands so you can run a Sockeon server alongside your Laravel app without a standalone `server.php` bootstrap.

## Requirements

- PHP 8.3+
- Laravel 11, 12, or 13
- [sockeon/sockeon](https://github.com/sockeon/sockeon) ^3.0
- ext-sockets, ext-openssl (required by Sockeon)
- ext-openswoole (optional, for `engine=swoole`)
- ext-redis (optional, for multi-node scaling)

## Installation

```bash
composer require sockeon/laravel
```

Publish the config (optional — the package merges defaults automatically):

```bash
php artisan vendor:publish --tag=sockeon-config
```

## Quick start

1. Create a controller in `app/Sockeon/Controllers/`:

```php
<?php

namespace App\Sockeon\Controllers;

use Sockeon\Sockeon\Controllers\SocketController;
use Sockeon\Sockeon\Http\Attributes\HttpRoute;
use Sockeon\Sockeon\Http\Request;
use Sockeon\Sockeon\Http\Response;
use Sockeon\Sockeon\WebSocket\Attributes\OnConnect;
use Sockeon\Sockeon\WebSocket\Attributes\SocketOn;

class ChatController extends SocketController
{
    #[OnConnect]
    public function onConnect(string $clientId): void
    {
        $this->emit($clientId, 'welcome', ['clientId' => $clientId]);
    }

    #[SocketOn('chat.message')]
    public function onMessage(string $clientId, array $data): void
    {
        $this->broadcast('chat.message', [
            'clientId' => $clientId,
            'message' => $data['message'] ?? '',
        ]);
    }

    #[HttpRoute('GET', '/api/status')]
    public function status(Request $request): Response
    {
        return Response::json(['clients' => $this->getClientCount()]);
    }
}
```

2. Start the server:

```bash
php artisan sockeon:serve
```

Controllers in `app/Sockeon/Controllers` are auto-discovered by default. You can also register classes manually in `config/sockeon.php` under `controllers`.

## Configuration

All options live in `config/sockeon.php`. Key environment variables:

| Variable | Default | Description |
|----------|---------|-------------|
| `SOCKEON_HOST` | `0.0.0.0` | Bind address |
| `SOCKEON_PORT` | `6001` | Listen port |
| `SOCKEON_DEBUG` | `false` | Verbose logging |
| `SOCKEON_ENGINE` | `stream_select` | `stream_select` or `swoole` |
| `SOCKEON_AUTO_DISCOVER` | `true` | Scan `app/Sockeon/Controllers` |

See the [Sockeon docs](https://sockeon.github.io) for survivability, scaling, CORS, and rate limiting options.

### Middleware

Register global middleware in `config/sockeon.php`:

```php
'middleware' => [
    'http' => [
        App\Sockeon\Middleware\AuthMiddleware::class,
    ],
    'websocket' => [
        App\Sockeon\Middleware\AuthenticateSocket::class,
    ],
    'handshake' => [
        App\Sockeon\Middleware\VerifyAuthKey::class,
    ],
],
```

Per-route middleware uses `HttpRoute` / `SocketOn` attributes on your controllers. See [Middleware](https://sockeon.github.io/v3.0/core/middleware.md).

## Artisan commands

| Command | Description |
|---------|-------------|
| `sockeon:serve` | Start the WebSocket/HTTP server |

## Facade

```php
use Sockeon\Laravel\Facades\Sockeon;

$server = Sockeon::server();
$server->broadcast('notification', ['message' => 'Hello']);
```

The server instance is created once per process and controllers are resolved through Laravel's container (constructor injection works).

## Testing

```bash
composer test
```

## Documentation

- [Sockeon documentation](https://sockeon.github.io)
- [Quick start guide](https://sockeon.github.io/v3.0/getting-started/quick-start)

## Security

If you discover a security vulnerability, please email [xentixar@gmail.com](mailto:xentixar@gmail.com).

## License

MIT — see [LICENSE](LICENSE).
