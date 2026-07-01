<?php

namespace Sockeon\Laravel\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Sockeon\Laravel\SockeonManager;
use Sockeon\Laravel\SockeonServiceProvider;
use Sockeon\Laravel\Tests\Fixtures\DiscoverableHttpMiddleware;
use Sockeon\Laravel\Tests\Fixtures\RecordingHttpMiddleware;
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
        $this->assertTrue(config('sockeon.logging.to_console'));
        $this->assertTrue(config('sockeon.logging.to_file'));
    }

    public function test_manager_creates_server(): void
    {
        $server = $this->app->make(SockeonManager::class)->server();

        $this->assertInstanceOf(Server::class, $server);
    }

    public function test_manager_registers_configured_middleware(): void
    {
        config()->set('sockeon.middleware.http', [RecordingHttpMiddleware::class]);

        $server = $this->app->make(SockeonManager::class)->server();
        $stack = (new \ReflectionProperty($server->getMiddleware(), 'httpStack'))->getValue($server->getMiddleware());

        $this->assertSame([RecordingHttpMiddleware::class], $stack);
    }

    public function test_manager_discovers_middleware_when_enabled(): void
    {
        config()->set('sockeon.middleware', [
            'auto_discover' => true,
            'path' => __DIR__.'/../Fixtures',
            'namespace' => 'Sockeon\\Laravel\\Tests\\Fixtures',
            'http' => [],
            'websocket' => [],
            'handshake' => [],
        ]);

        $server = $this->app->make(SockeonManager::class)->server();
        $stack = (new \ReflectionProperty($server->getMiddleware(), 'httpStack'))->getValue($server->getMiddleware());

        $this->assertContains(DiscoverableHttpMiddleware::class, $stack);
    }
}
