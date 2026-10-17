<?php
declare(strict_types=1);

namespace App\Support;

/** Per-session synchronizer token, required on every POST request (forms and Ajax). */
class Csrf
{
    public const FIELD = 'csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::FIELD]) || !is_string($_SESSION[self::FIELD])) {
            $_SESSION[self::FIELD] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::FIELD];
    }

    /** Hidden input to place inside every POST form. */
    public static function field(): string
    {
        return '<input type="hidden" name="' . self::FIELD . '" value="' . e(self::token()) . '">';
    }

    public static function isValid(): bool
    {
        $sent = $_POST[self::FIELD] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return is_string($sent) && $sent !== '' && hash_equals(self::token(), $sent);
    }
}
