<?php

namespace Sockeon\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Sockeon\Laravel\SockeonManager;
use Sockeon\Sockeon\Connection\Server;

/**
 * @method static Server server()
 * @method static void serve()
 *
 * @see SockeonManager
 */
class Sockeon extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SockeonManager::class;
    }
}
