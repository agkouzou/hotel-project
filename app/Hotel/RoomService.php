<?php
declare(strict_types=1);

namespace App\Hotel;

use App\Support\Db;
use App\Support\Validator;

class RoomService
{
    private const SELECT = 'SELECT r.*, t.title AS type_title
                            FROM room r JOIN room_type t ON t.type_id = r.type_id';

    public static function cities(): array
    {
        return Db::all('SELECT DISTINCT city FROM room ORDER BY city');
    }

    public static function find(int $roomId): ?array
    {
        return Db::one(self::SELECT . ' WHERE r.room_id = :id', ['id' => $roomId]);
    }

    /** True when no booking overlaps the [in, out) period. */
    public static function isAvailable(int $roomId, string $in, string $out): bool
    {
        return Db::one(
            'SELECT 1 FROM booking
             WHERE room_id = :room AND check_in_date < :out AND check_out_date > :in
             LIMIT 1',
            ['room' => $roomId, 'in' => $in, 'out' => $out]
        ) === null;
    }

    /**
     * Normalises raw GET parameters into a filter array (never trusted as-is).
     * Keys: check_in, check_out, cities[], types[], price_min, price_max, guests.
     */
    public static function filtersFromRequest(array $q): array
    {
        $ints = fn($v) => array_values(array_filter(array_map('intval', array_filter((array) $v, 'is_scalar')), fn($i) => $i > 0));
        $strs = fn($v) => array_values(array_filter(array_map('strval', array_filter((array) $v, 'is_scalar')), fn($s) => $s !== ''));
        $num = fn($v) => (isset($v) && is_scalar($v) && $v !== '' && is_numeric($v)) ? max(0, (int) $v) : null;

        return [
            'check_in' => is_scalar($q['check_in'] ?? null) ? (string) $q['check_in'] : '',
            'check_out' => is_scalar($q['check_out'] ?? null) ? (string) $q['check_out'] : '',
            'cities' => $strs($q['cities'] ?? []),
            'types' => $ints($q['types'] ?? []),
            'price_min' => $num($q['price_min'] ?? null),
            'price_max' => $num($q['price_max'] ?? null),
            'guests' => $num($q['guests'] ?? null),
        ];
    }

    /** Check-in, check-out and city are required for a user-submitted search. */
    public static function validateSearch(array $f): ?string
    {
        $error = Validator::stay($f['check_in'], $f['check_out']);
        if ($error === null && !$f['cities']) {
            $error = 'Please choose a city.';
        }
        return $error;
    }

    public static function search(array $f): array
    {
        $sql = self::SELECT . ' WHERE 1 = 1';
        $params = [];

        if ($f['cities']) {
            $sql .= ' AND r.city IN (' . self::placeholders('city', $f['cities'], $params) . ')';
        }
        if ($f['types']) {
            $sql .= ' AND r.type_id IN (' . self::placeholders('type', $f['types'], $params) . ')';
        }
        if ($f['price_min'] !== null) {
            $sql .= ' AND r.price >= :price_min';
            $params['price_min'] = $f['price_min'];
        }
        if ($f['price_max'] !== null) {
            $sql .= ' AND r.price <= :price_max';
            $params['price_max'] = $f['price_max'];
        }
        if ($f['guests'] !== null) {
            $sql .= ' AND r.count_of_guests >= :guests';
            $params['guests'] = $f['guests'];
        }
        if (Validator::date($f['check_in']) && Validator::date($f['check_out']) && $f['check_out'] > $f['check_in']) {
            $sql .= ' AND NOT EXISTS (SELECT 1 FROM booking b
                                      WHERE b.room_id = r.room_id
                                        AND b.check_in_date < :out AND b.check_out_date > :in)';
            $params['in'] = $f['check_in'];
            $params['out'] = $f['check_out'];
        }

        return Db::all($sql . ' ORDER BY r.price ASC, r.room_id ASC', $params);
    }

    /** Builds ":prefix0, :prefix1, ..." and fills $params. */
    private static function placeholders(string $prefix, array $values, array &$params): string
    {
        $names = [];
        foreach ($values as $i => $value) {
            $name = $prefix . $i;
            $names[] = ':' . $name;
            $params[$name] = $value;
        }
        return implode(', ', $names);
    }
}
