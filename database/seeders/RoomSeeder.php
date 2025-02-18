<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            'ห้อง 101', 'ห้อง 102', 'ห้อง 103',
            'ห้อง 104', 'ห้อง 105', 'ห้อง 106',
            'ห้อง 107', 'ห้อง 108', 'ห้อง 109'
        ];

        foreach ($rooms as $room) {
            Room::create(['name' => $room]);
        }
    }
}
