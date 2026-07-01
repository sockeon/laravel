<?php

namespace Sockeon\Laravel\Tests\Fixtures;

use Sockeon\Sockeon\Connection\Server;
use Sockeon\Sockeon\Contracts\Http\HttpMiddleware;
use Sockeon\Sockeon\Http\Request;

class DiscoverableHttpMiddleware implements HttpMiddleware
{
    public function handle(Request $request, callable $next, Server $server): mixed
    {
        return $next($request);
    }
}
