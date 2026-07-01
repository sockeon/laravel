<?php

namespace Sockeon\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'sockeon:install
                            {--force : Overwrite the published configuration file}';

    protected $description = 'Publish Sockeon config and scaffold application directories';

    public function handle(): int
    {
        $this->call('vendor:publish', array_filter([
            '--tag' => 'sockeon-config',
            '--force' => $this->option('force') ?: null,
        ]));

        File::ensureDirectoryExists(config('sockeon.controllers_path', app_path('Sockeon/Controllers')));
        File::ensureDirectoryExists(config('sockeon.middleware.path', app_path('Sockeon/Middleware')));

        $this->components->info('Sockeon installed successfully.');
        $this->line('  Config:      config/sockeon.php');
        $this->line('  Controllers: '.config('sockeon.controllers_path', app_path('Sockeon/Controllers')));
        $this->line('  Middleware:  '.config('sockeon.middleware.path', app_path('Sockeon/Middleware')));
        $this->newLine();
        $this->line('Run <fg=cyan>php artisan sockeon:serve</> to start the server.');

        return self::SUCCESS;
    }
}
