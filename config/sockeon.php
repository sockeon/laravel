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

        // Number of worker processes. Defaults to CPU core count when null.
        'worker_num' => (int) env('SOCKEON_SWOOLE_WORKER_NUM', 1),

        // Maximum concurrent connections the Swoole server accepts.
        'max_connection' => (int) env('SOCKEON_SWOOLE_MAX_CONNECTION', 10_000),

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

            'host' => env('SOCKEON_REDIS_HOST', '127.0.0.1'),

            'port' => (int) env('SOCKEON_REDIS_PORT', 6379),

            // Pub/sub channel used for cross-node broadcasts.
            'channel' => env('SOCKEON_REDIS_CHANNEL', 'sockeon:broadcast'),

        ],

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

    ],

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

];
