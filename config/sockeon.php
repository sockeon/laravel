<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Host
    |--------------------------------------------------------------------------
    |
    | The network interface the Sockeon server binds to. Use 0.0.0.0 to listen
    | on all interfaces, or 127.0.0.1 to restrict access to the local machine.
    |
    */

    'host' => env('SOCKEON_HOST', '0.0.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Server Port
    |--------------------------------------------------------------------------
    |
    | The TCP port the Sockeon server listens on for WebSocket and HTTP
    | connections. This runs separately from Laravel's HTTP server.
    |
    */

    'port' => (int) env('SOCKEON_PORT', 6001),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, Sockeon logs additional diagnostic output. This should be
    | disabled in production.
    |
    */

    'debug' => (bool) env('SOCKEON_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Sockeon's built-in logger. When "level" is null, debug mode uses "debug"
    | and production uses "info". Logs are written to storage/logs/sockeon by
    | default when file logging is enabled.
    |
    */

    'logging' => [

        'level' => env('SOCKEON_LOG_LEVEL'),

        'to_console' => (bool) env('SOCKEON_LOG_CONSOLE', true),

        'to_file' => (bool) env('SOCKEON_LOG_FILE', true),

        'directory' => env('SOCKEON_LOG_DIRECTORY'),

        'separate_files' => (bool) env('SOCKEON_LOG_SEPARATE_FILES', false),

    ],

    /*
    |--------------------------------------------------------------------------
    | Runtime Engine
    |--------------------------------------------------------------------------
    |
    | The engine that drives the event loop. Supported values:
    |
    | - "stream_select" — pure PHP, no extra extensions (default)
    | - "swoole"        — requires ext-openswoole for high concurrency
    |
    */

    'engine' => env('SOCKEON_ENGINE', 'stream_select'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Key
    |--------------------------------------------------------------------------
    |
    | An optional shared secret clients must provide during the WebSocket
    | handshake. Set to null to allow unauthenticated connections.
    |
    */

    'auth_key' => env('SOCKEON_AUTH_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Queue File
    |--------------------------------------------------------------------------
    |
    | Path to a file-based queue used for cross-process message delivery.
    | Leave null to disable file-based queuing.
    |
    */

    'queue_file' => env('SOCKEON_QUEUE_FILE'),

    /*
    |--------------------------------------------------------------------------
    | Health Check Path
    |--------------------------------------------------------------------------
    |
    | When set, Sockeon exposes an HTTP health-check endpoint at this path.
    | Set to null to disable the endpoint.
    |
    */

    'health_check_path' => env('SOCKEON_HEALTH_CHECK_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Maximum Message Size
    |--------------------------------------------------------------------------
    |
    | The largest incoming WebSocket message (in bytes) the server accepts.
    | Messages exceeding this limit are rejected.
    |
    */

    'max_message_size' => (int) env('SOCKEON_MAX_MESSAGE_SIZE', 65536),

    /*
    |--------------------------------------------------------------------------
    | System Controllers
    |--------------------------------------------------------------------------
    |
    | When enabled, Sockeon registers built-in controllers for room management
    | and other framework features. Disable only if you provide your own.
    |
    */

    'register_system_controllers' => (bool) env('SOCKEON_REGISTER_SYSTEM_CONTROLLERS', true),

    /*
    |--------------------------------------------------------------------------
    | Survivability
    |--------------------------------------------------------------------------
    |
    | Hard connection limits and heartbeat settings that protect the server
    | from resource exhaustion. These caps are always enforced.
    |
    */

    'survivability' => [

        // Maximum number of simultaneous client connections.
        'max_connections' => (int) env('SOCKEON_MAX_CONNECTIONS', 10_000),

        // Seconds of inactivity before a connection is considered idle.
        'heartbeat_idle_time' => (int) env('SOCKEON_HEARTBEAT_IDLE_TIME', 300),

        // How often (in seconds) the server scans for idle connections.
        'heartbeat_check_interval' => (int) env('SOCKEON_HEARTBEAT_CHECK_INTERVAL', 60),

    ],

    /*
    |--------------------------------------------------------------------------
    | Swoole Engine
    |--------------------------------------------------------------------------
    |
    | Options used when engine is set to "swoole". Ignored for stream_select.
    |
    */

    'swoole' => [

        'worker_num' => (int) env('SOCKEON_SWOOLE_WORKER_NUM', 1),

        'task_worker_num' => (int) env('SOCKEON_SWOOLE_TASK_WORKER_NUM', 0),

        'max_connection' => (int) env('SOCKEON_SWOOLE_MAX_CONNECTION', 10_000),

        'client_table_size' => env('SOCKEON_SWOOLE_CLIENT_TABLE_SIZE') !== null
            ? (int) env('SOCKEON_SWOOLE_CLIENT_TABLE_SIZE')
            : null,

        'coroutine_dispatch' => (bool) env('SOCKEON_SWOOLE_COROUTINE_DISPATCH', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | Horizontal Scaling
    |--------------------------------------------------------------------------
    |
    | Configure multi-node deployments. Set publisher and registry to "redis"
    | (requires ext-redis) to broadcast events and share client state across
    | nodes. Use "local" for single-node setups.
    |
    */

    'scale' => [

        // Unique identifier for this server instance in a cluster.
        'node_id' => env('SOCKEON_NODE_ID', 'node-1'),

        // Broadcast backend: "local" or "redis".
        'publisher' => env('SOCKEON_SCALE_PUBLISHER', 'local'),

        // Client registry backend: "local" or "redis".
        'registry' => env('SOCKEON_SCALE_REGISTRY', 'local'),

        'redis' => [

            'host' => env('SOCKEON_REDIS_HOST', env('REDIS_HOST', '127.0.0.1')),

            'port' => (int) env('SOCKEON_REDIS_PORT', env('REDIS_PORT', 6379)),

            'password' => env('SOCKEON_REDIS_PASSWORD', env('REDIS_PASSWORD')),

            'database' => (int) env('SOCKEON_REDIS_DB', env('REDIS_DB', 0)),

            'channel' => env('SOCKEON_REDIS_CHANNEL', 'sockeon:broadcast'),

            'prefix' => env('SOCKEON_REDIS_PREFIX', 'sockeon:'),

        ],

        'presence_ttl' => (int) env('SOCKEON_PRESENCE_TTL', 300),

    ],

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS)
    |--------------------------------------------------------------------------
    |
    | Controls which origins may access the HTTP and WebSocket endpoints.
    | Additional keys such as allowed_methods, allowed_headers,
    | allow_credentials, and max_age are also supported.
    |
    */

    'cors' => [

        'allowed_origins' => ['*'],

        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH', 'HEAD'],

        'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],

        'allow_credentials' => (bool) env('SOCKEON_CORS_ALLOW_CREDENTIALS', false),

        'max_age' => (int) env('SOCKEON_CORS_MAX_AGE', 86400),

    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Protects against abuse on HTTP requests, WebSocket messages, and new
    | connections. Set to null to disable. Connection caps are enforced at
    | accept time even when "enabled" is false.
    |
    */

    'rate_limit' => env('SOCKEON_RATE_LIMIT_ENABLED', false) ? [
        'enabled' => true,
        'maxHttpRequestsPerIp' => (int) env('SOCKEON_RATE_LIMIT_HTTP_PER_IP', 1000),
        'httpTimeWindow' => (int) env('SOCKEON_RATE_LIMIT_HTTP_WINDOW', 60),
        'maxWebSocketMessagesPerClient' => (int) env('SOCKEON_RATE_LIMIT_WS_PER_CLIENT', 2000),
        'webSocketTimeWindow' => (int) env('SOCKEON_RATE_LIMIT_WS_WINDOW', 60),
        'maxConnectionsPerIp' => (int) env('SOCKEON_RATE_LIMIT_CONNECTIONS_PER_IP', 500),
        'connectionTimeWindow' => (int) env('SOCKEON_RATE_LIMIT_CONNECTION_WINDOW', 60),
        'maxGlobalConnections' => (int) env('SOCKEON_RATE_LIMIT_GLOBAL_CONNECTIONS', 50_000),
    ] : null,

    /*
    |--------------------------------------------------------------------------
    | Reverse Proxy
    |--------------------------------------------------------------------------
    |
    | Trust X-Forwarded-* headers when running behind a load balancer or CDN.
    | Set to true to trust all proxies, false to trust none, or provide an
    | array of trusted proxy IP addresses and CIDR ranges.
    |
    */

    'trust_proxy' => filter_var(env('SOCKEON_TRUST_PROXY', false), FILTER_VALIDATE_BOOL),

    'proxy_headers' => null,

    /*
    |--------------------------------------------------------------------------
    | Socket Controllers
    |--------------------------------------------------------------------------
    |
    | Classes that handle WebSocket events and Sockeon HTTP routes. Each must
    | extend Sockeon\Sockeon\Controllers\SocketController. When auto_discover
    | is enabled, all controllers in controllers_path are registered
    | automatically in addition to any listed here.
    |
    */

    'controllers' => [

        // App\Sockeon\Controllers\ChatController::class,

    ],

    // Automatically register controllers found in controllers_path.
    'auto_discover' => (bool) env('SOCKEON_AUTO_DISCOVER', true),

    // Directory scanned when auto_discover is enabled.
    'controllers_path' => app_path('Sockeon/Controllers'),

    // PHP namespace that maps to controllers_path.
    'controllers_namespace' => 'App\\Sockeon\\Controllers',

    /*
    |--------------------------------------------------------------------------
    | Global Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware classes registered on every Sockeon HTTP request, WebSocket
    | event, or handshake. Each class must implement the matching Sockeon
    | contract: HttpMiddleware, WebsocketMiddleware, or HandshakeMiddleware.
    |
    | Route-specific middleware is still declared on controller attributes
    | (HttpRoute, SocketOn). Sockeon instantiates middleware with "new", so
    | constructors cannot use Laravel dependency injection — resolve services
    | from the container inside handle() if needed.
    |
    */

    'middleware' => [

        'auto_discover' => (bool) env('SOCKEON_MIDDLEWARE_AUTO_DISCOVER', false),

        'path' => app_path('Sockeon/Middleware'),

        'namespace' => 'App\\Sockeon\\Middleware',

        'http' => [
            // App\Sockeon\Middleware\AuthMiddleware::class,
        ],

        'websocket' => [
            // App\Sockeon\Middleware\AuthenticateSocket::class,
        ],

        'handshake' => [
            // App\Sockeon\Middleware\VerifyAuthKey::class,
        ],

    ],

];
