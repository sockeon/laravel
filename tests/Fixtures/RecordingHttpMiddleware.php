<?php

namespace Sockeon\Laravel\Tests\Fixtures;

use Sockeon\Sockeon\Connection\Server;
use Sockeon\Sockeon\Contracts\Http\HttpMiddleware;
use Sockeon\Sockeon\Http\Request;

class RecordingHttpMiddleware implements HttpMiddleware
{
    public static bool $handled = false;

    public function handle(Request $request, callable $next, Server $server): mixed
    {
        self::$handled = true;

        return $next($request);
    }
}
