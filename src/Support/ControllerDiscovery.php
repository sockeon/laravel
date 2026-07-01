<?php

namespace Sockeon\Laravel\Support;

use Sockeon\Sockeon\Controllers\SocketController;

class ControllerDiscovery
{
    /**
     * @return list<class-string<SocketController>>
     */
    public function discover(string $path, string $namespace): array
    {
        if (! is_dir($path)) {
            return [];
        }

        $classes = [];

        foreach (glob($path.'/*.php') ?: [] as $file) {
            $class = $namespace.'\\'.basename($file, '.php');

            if (class_exists($class) && is_subclass_of($class, SocketController::class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}
