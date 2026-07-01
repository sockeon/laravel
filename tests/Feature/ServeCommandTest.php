<?php

namespace Sockeon\Laravel\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Sockeon\Laravel\SockeonManager;
use Sockeon\Laravel\SockeonServiceProvider;
use Sockeon\Sockeon\Connection\Server;

class ServeCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SockeonServiceProvider::class];
    }

    public function test_config_merges(): void
    {
        $this->assertIsArray(config('sockeon'));
        $this->assertSame('0.0.0.0', config('sockeon.host'));
        $this->assertSame(6001, config('sockeon.port'));
    }

    public function test_manager_creates_server(): void
    {
        $server = $this->app->make(SockeonManager::class)->server();

        $this->assertInstanceOf(Server::class, $server);
    }
}
