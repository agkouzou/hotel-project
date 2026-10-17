<?php
declare(strict_types=1);

namespace App\Support;

/** Minimal PSR-4 style autoloader: App\Hotel\RoomService => app/Hotel/RoomService.php */
class Autoloader
{
    public static function register(string $namespace, string $baseDir): void
    {
        spl_autoload_register(function (string $class) use ($namespace, $baseDir): void {
            $prefix = $namespace . '\\';
            if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                return;
            }
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . '/' . str_replace('\\', '/', $relative) . '.php';
            if (is_file($file)) {
                require $file;
            }
        });
    }
}
