<?php
declare(strict_types=1);

namespace App\Hotel;

use App\Support\Db;

class BookingService
{
    public static function nights(string $in, string $out): int
    {
        return (int) (new \DateTime($in))->diff(new \DateTime($out))->days;
    }

    /**
     * Creates a booking if the room is still free. The room row is locked inside
     * the transaction so two concurrent requests cannot book the same period.
     * Returns the booking id, or null when the room is not available.
     */
    public static function create(int $userId, int $roomId, string $in, string $out): ?int
    {
        $pdo = Db::pdo();
        $pdo->beginTransaction();
        try {
            $room = Db::one('SELECT price FROM room WHERE room_id = :id FOR UPDATE', ['id' => $roomId]);
            if ($room === null || !RoomService::isAvailable($roomId, $in, $out)) {
                $pdo->rollBack();
                return null;
            }
            Db::query(
                'INSERT INTO booking (user_id, room_id, check_in_date, check_out_date, total_price)
                 VALUES (:user, :room, :in, :out, :total)',
                [
                    'user' => $userId,
                    'room' => $roomId,
                    'in' => $in,
                    'out' => $out,
                    'total' => self::nights($in, $out) * (int) $room['price'],
                ]
            );
            $id = (int) $pdo->lastInsertId();
            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function forUser(int $userId): array
    {
        return Db::all(
            'SELECT b.*, r.name, r.photo_url, r.description_short, t.title AS type_title
             FROM booking b
             JOIN room r ON r.room_id = b.room_id
             JOIN room_type t ON t.type_id = r.type_id
             WHERE b.user_id = :user
             ORDER BY b.created_time DESC, b.booking_id DESC',
            ['user' => $userId]
        );
    }
}
