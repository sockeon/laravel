<?php

namespace Sockeon\Laravel\Console;

use Illuminate\Console\Command;
use Sockeon\Laravel\SockeonManager;

class ServeCommand extends Command
{
    protected $signature = 'sockeon:serve';

    protected $description = 'Start the Sockeon WebSocket and HTTP server';

    public function handle(SockeonManager $manager): int
    {
        $server = $manager->server();
        $engine = $server->getEngine()->getName();
        $survivability = $server->getSurvivabilityConfig();
        $swooleMax = (int) config('sockeon.swoole.max_connection', 10_000);
        $maxConnections = $engine === 'swoole'
            ? min($swooleMax, $survivability->getMaxConnections())
            : $survivability->getMaxConnections();
        $scale = $server->getScaleConfig();

        $this->info("Sockeon server listening on {$server->getHost()}:{$server->getPort()}");
        $this->line("Engine:          {$engine}");
        $this->line("Max connections: {$maxConnections}");
        $this->line("Node ID:         {$scale->getNodeId()}");
        $this->line("Scale:           publisher={$scale->getPublisher()} registry={$scale->getRegistry()}");

        $manager->serve();

        return self::SUCCESS;
    }
}
