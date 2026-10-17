<?php
declare(strict_types=1);

namespace App\Support;

class Config
{
    private static array $data = [];

    public static function load(string $file): void
    {
        $json = file_get_contents($file);
        if ($json === false) {
            throw new \RuntimeException("Cannot read config file: $file");
        }
        self::$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /** Dot notation: Config::get('db.host') */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$data;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }
}
