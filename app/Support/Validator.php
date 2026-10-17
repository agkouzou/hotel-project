<?php
declare(strict_types=1);

namespace App\Support;

/** Back-end validation helpers; each returns an error message or null. */
class Validator
{
    public static function required(?string $value, string $label): ?string
    {
        return trim((string) $value) === '' ? "$label is required." : null;
    }

    public static function email(?string $value): ?string
    {
        return filter_var((string) $value, FILTER_VALIDATE_EMAIL) ? null : 'Please enter a valid email address.';
    }

    public static function date(?string $value): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', (string) $value);
        return $d !== false && $d->format('Y-m-d') === $value;
    }

    /** Validates a check-in/check-out pair. */
    public static function stay(?string $in, ?string $out): ?string
    {
        if (!self::date($in) || !self::date($out)) {
            return 'Please choose valid check-in and check-out dates.';
        }
        if ($in < date('Y-m-d')) {
            return 'Check-in date cannot be in the past.';
        }
        if ($out <= $in) {
            return 'Check-out date must be after check-in date.';
        }
        return null;
    }
}
