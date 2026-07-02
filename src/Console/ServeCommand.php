<?php

namespace Sockeon\Laravel\Console;

use Illuminate\Console\Command;
use Sockeon\Laravel\SockeonManager;
use Sockeon\Sockeon\Config\SwooleEngineConfig;
use Sockeon\Sockeon\Connection\Server;

class ServeCommand extends Command
{
    protected $signature = 'sockeon:serve';

    protected $description = 'Start the Sockeon WebSocket and HTTP server';

    public function handle(): int
    {
        $this->bootstrapSwooleMemoryLimit();

        /** @var SockeonManager $manager */
        $manager = app(SockeonManager::class);
        $server = $manager->server();
        $this->printStartupSummary($manager, $server);

        $server->getLogger()->setLogToConsole(false);

        $manager->serve();

        return self::SUCCESS;
    }

    private function printStartupSummary(SockeonManager $manager, Server $server): void
    {
        $engine = $server->getEngine()->getName();
        $survivability = $server->getSurvivabilityConfig();
        $swoole = config('sockeon.swoole', []);
        $swooleMax = (int) ($swoole['max_connection'] ?? 10_000);
        $maxConnections = $engine === 'swoole'
            ? min($swooleMax, $survivability->getMaxConnections())
            : $survivability->getMaxConnections();
        $scale = $server->getScaleConfig();
        $address = "{$server->getHost()}:{$server->getPort()}";

        $rows = [
            ['Address', $address],
            ['Engine', $engine],
            ['Max connections', (string) $maxConnections],
            ['Debug', config('sockeon.debug', false) ? 'on' : 'off'],
            ['Node ID', $scale->getNodeId()],
            ['Publisher', $scale->getPublisher()],
            ['Registry', $scale->getRegistry()],
        ];

        if ($scale->isRedisPublisher() || $scale->isRedisRegistry()) {
            $rows[] = ['Redis', "{$scale->getRedisHost()}:{$scale->getRedisPort()}"];
        }

        if ($engine === 'swoole') {
            $rows[] = ['Swoole workers', (string) ($swoole['worker_num'] ?? 1)];
            $rows[] = ['Task workers', (string) ($swoole['task_worker_num'] ?? 0)];
            $rows[] = ['PHP memory limit', ini_get('memory_limit') ?: 'unknown'];
        }

        $logging = config('sockeon.logging', []);
        $logTargets = array_filter([
            ($logging['to_console'] ?? true) ? 'console' : null,
            ($logging['to_file'] ?? true) ? 'file' : null,
        ]);
        $rows[] = ['Logging', implode(' + ', $logTargets) ?: 'off'];
        $rows[] = ['Rate limiting', $server->isRateLimitingEnabled() ? 'on' : 'off'];
        $rows[] = ['System controllers', config('sockeon.register_system_controllers', true) ? 'on' : 'off'];

        $controllerAutoDiscover = (bool) config('sockeon.auto_discover', true);
        $controllers = $manager->registeredControllers();
        $rows[] = [
            'Controllers',
            count($controllers).' registered'
                .($controllerAutoDiscover ? ' (auto-discover on)' : ' (auto-discover off)'),
        ];

        $middlewareConfig = config('sockeon.middleware', []);
        $middlewareAutoDiscover = (bool) ($middlewareConfig['auto_discover'] ?? false);
        $middleware = $manager->resolvedMiddleware();
        $middlewareTotal = count($middleware['http'])
            + count($middleware['websocket'])
            + count($middleware['handshake']);
        $rows[] = [
            'Middleware',
            "{$middlewareTotal} registered"
                .' — HTTP '.count($middleware['http'])
                .', WebSocket '.count($middleware['websocket'])
                .', Handshake '.count($middleware['handshake'])
                .($middlewareAutoDiscover ? ' (auto-discover on)' : ' (auto-discover off)'),
        ];

        $this->components->info('Sockeon server starting…');
        $this->table(['', ''], $rows);
        $this->newLine();
    }

    private function bootstrapSwooleMemoryLimit(): void
    {
        if (config('sockeon.engine', 'stream_select') !== 'swoole') {
            return;
        }

        /** @var array<string, mixed> $swoole */
        $swoole = config('sockeon.swoole', []);
        $limit = (new SwooleEngineConfig($swoole))->getMemoryLimit();
        ini_set('memory_limit', $limit);
    }
}
