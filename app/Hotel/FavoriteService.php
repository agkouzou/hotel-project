<?php
declare(strict_types=1);

namespace App\Hotel;

use App\Support\Db;

class FavoriteService
{
    public static function isFavorite(int $userId, int $roomId): bool
    {
        return Db::one(
            'SELECT 1 FROM favorite WHERE user_id = :user AND room_id = :room',
            ['user' => $userId, 'room' => $roomId]
        ) !== null;
    }

    /** Adds or removes the favorite; returns the new state (true = favorite). */
    public static function toggle(int $userId, int $roomId): bool
    {
        if (self::isFavorite($userId, $roomId)) {
            Db::query('DELETE FROM favorite WHERE user_id = :user AND room_id = :room',
                ['user' => $userId, 'room' => $roomId]);
            return false;
        }
        Db::query('INSERT IGNORE INTO favorite (user_id, room_id) VALUES (:user, :room)',
            ['user' => $userId, 'room' => $roomId]);
        return true;
    }

    public static function forUser(int $userId): array
    {
        return Db::all(
            'SELECT f.created_time AS favorited_at, r.*, t.title AS type_title
             FROM favorite f
             JOIN room r ON r.room_id = f.room_id
             JOIN room_type t ON t.type_id = r.type_id
             WHERE f.user_id = :user
             ORDER BY f.created_time DESC, f.room_id DESC',
            ['user' => $userId]
        );
    }
}
