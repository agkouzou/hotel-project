<?php
declare(strict_types=1);

namespace App\Hotel;

use App\Support\Db;

class RoomTypeService
{
    public static function all(): array
    {
        return Db::all('SELECT type_id, title FROM room_type ORDER BY type_id');
    }
}
