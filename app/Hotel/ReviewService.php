<?php
declare(strict_types=1);

namespace App\Hotel;

use App\Support\Db;

class ReviewService
{
    public static function forRoom(int $roomId): array
    {
        return Db::all(
            'SELECT rv.review_id, rv.rate, rv.comment, rv.created_time, u.name AS user_name
             FROM review rv
             LEFT JOIN user u ON u.user_id = rv.user_id
             WHERE rv.room_id = :room
             ORDER BY rv.created_time DESC, rv.review_id DESC',
            ['room' => $roomId]
        );
    }

    public static function forUser(int $userId): array
    {
        return Db::all(
            'SELECT rv.review_id, rv.rate, rv.comment, rv.created_time, r.room_id, r.name AS room_name
             FROM review rv
             JOIN room r ON r.room_id = rv.room_id
             WHERE rv.user_id = :user
             ORDER BY rv.created_time DESC, rv.review_id DESC',
            ['user' => $userId]
        );
    }

    public static function create(int $userId, int $roomId, int $rate, ?string $comment): void
    {
        $pdo = Db::pdo();
        $pdo->beginTransaction();
        try {
            Db::query(
                'INSERT INTO review (room_id, user_id, rate, comment) VALUES (:room, :user, :rate, :comment)',
                ['room' => $roomId, 'user' => $userId, 'rate' => $rate, 'comment' => $comment]
            );
            // Keep the denormalised room statistics in sync.
            Db::query(
                'UPDATE room SET
                     avg_reviews = (SELECT AVG(rate) FROM review WHERE room_id = :r1),
                     count_reviews = (SELECT COUNT(*) FROM review WHERE room_id = :r2)
                 WHERE room_id = :r3',
                ['r1' => $roomId, 'r2' => $roomId, 'r3' => $roomId]
            );
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
