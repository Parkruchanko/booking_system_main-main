<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return view('rooms.index', compact('rooms'));
    }

    // Controller for showing room and available slots
public function show(Room $room, Request $request)
{
    // รับค่า weekOffset จาก URL ถ้าไม่มีให้เป็น 0
    $weekOffset = intval($request->input('week', 0));
    $weeks = intval($request->input('weeks', 1)); // จำนวนสัปดาห์ที่เลือก

    // คำนวณวันที่เริ่มต้นและสิ้นสุดของสัปดาห์
    $startOfWeek = Carbon::now('Asia/Bangkok')->startOfWeek()->addWeeks($weekOffset);
    $endOfWeek = $startOfWeek->copy()->addWeeks($weeks - 1)->endOfWeek();

    // สร้าง range สำหรับหลายสัปดาห์ที่เลือก
    $weeksRange = [];
    for ($i = 0; $i < $weeks; $i++) {
        $currentStart = $startOfWeek->copy()->addWeeks($i);
        $weeksRange[] = [
            'start' => $currentStart->toDateString(),
            'end' => $currentStart->copy()->endOfWeek()->toDateString(),
        ];
    }

    // ดึงข้อมูลการจองในช่วงวันที่ที่เลือก
    $bookings = Booking::where('room_id', $room->id)
        ->whereBetween('booking_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
        ->get();

    // ส่งข้อมูลไปยัง view
    return view('rooms.show', compact('room', 'bookings', 'weeksRange', 'weekOffset', 'weeks', 'startOfWeek'));
}

    


}