<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{

   


    public function storeBooking(Request $request, Room $room)
{
    $weekOffset = (int) $request->weeks; // จำนวนสัปดาห์ที่เลือก
    $selectedSlots = json_decode($request->input('selected_slots'), true); // คาบที่เลือก
    $bookingDates = json_decode($request->input('booking_dates'), true); // วันที่ที่เลือก

    // ตรวจสอบการเลือกวันในอดีต
    foreach ($bookingDates as $bookingDate) {
        $date = Carbon::parse($bookingDate);
        if ($date->isBefore(now())) {
            return redirect()->route('rooms.show', $room->id)
                ->with('error', 'คุณไม่สามารถเลือกวันในอดีตได้ กรุณาเลือกวันในอนาคต');
        }
    }

    // ตรวจสอบก่อนว่ามีคาบที่ถูกจองไปแล้วหรือไม่
    foreach ($bookingDates as $bookingDate) {
        foreach ($selectedSlots as $slotData) {
            list($day, $slot) = explode('_', $slotData);

            for ($i = 0; $i < $weekOffset; $i++) { 
                $currentDate = Carbon::parse($bookingDate)->addWeeks($i)->toDateString();

                // ตรวจสอบว่ามีการจองซ้ำแล้วหรือไม่
                $existingBooking = Booking::where('room_id', $room->id)
                    ->where('booking_date', $currentDate)
                    ->where('slot', $slot)
                    ->exists();

                // ถ้ามีการจองอยู่แล้ว ให้ **หยุดทั้งหมด** และขึ้น error
                if ($existingBooking) {
                    return redirect()->route('rooms.show', $room->id)
                        ->with('error', 'มีการจองคาบนี้ในวันที่ ' . $currentDate . ' แล้ว กรุณาเลือกคาบเรียนอื่น');
                }
            }
        }
    }

    // ✅ ถ้าผ่านการตรวจสอบทั้งหมด ค่อยเริ่มบันทึกลงฐานข้อมูล
    foreach ($bookingDates as $bookingDate) {
        foreach ($selectedSlots as $slotData) {
            list($day, $slot) = explode('_', $slotData);

            for ($i = 0; $i < $weekOffset; $i++) { 
                $currentDate = Carbon::parse($bookingDate)->addWeeks($i)->toDateString();

                // ตรวจสอบว่ามีการจองแล้วหรือยังในวันเดียวกัน
                $existingBooking = Booking::where('room_id', $room->id)
                    ->where('booking_date', $currentDate)
                    ->where('slot', $slot)
                    ->exists();

                // หากไม่มีการจองให้ทำการบันทึก
                if (!$existingBooking) {
                    Booking::create([
                        'room_id' => $room->id,
                        'booking_date' => $currentDate,
                        'name' => $request->name,
                        'reason' => $request->reason,
                        'day' => $day,
                        'slot' => $slot,
                        'weeks' => $weekOffset,
                        'user_id' => auth()->user()->id, // เพิ่ม user_id ที่นี่
                    ]);
                }
            }
        }
    }

    return redirect()->route('rooms.show', $room->id)->with('success', 'การจองห้องสำเร็จ');
}





    



    

    


    





    

    

    


    



    
    

    



   // ✅ ยืนยันข้อมูลก่อนจอง
   public function confirmBooking(Request $request, Room $room)
   {
       $request->validate([
           'room_id' => 'required|exists:rooms,id',
           'slots' => 'required|string', // JSON string ของคาบที่เลือก
       ]);

       // แปลงข้อมูลจาก JSON เป็นอาร์เรย์
       $selectedSlots = json_decode($request->slots, true);
       
       // รับค่า weekOffset จาก URL ถ้าไม่มีให้เป็น 0
       $weekOffset = (int) $request->query('week', 0);

       return view('rooms.confirmBooking', compact('selectedSlots', 'room', 'weekOffset'));
   }

    




    
    
    


    
    
    

    // ✅ ฟังก์ชันช่วยบันทึกการจองหลายสัปดาห์
private function saveBookings($roomId, $name, $reason, $selectedSlots, $weeks)
{
    $daysMap = [
        'อาทิตย์' => 'Sunday', 'จันทร์' => 'Monday', 'อังคาร' => 'Tuesday',
        'พุธ' => 'Wednesday', 'พฤหัสบดี' => 'Thursday', 'ศุกร์' => 'Friday', 'เสาร์' => 'Saturday'
    ];

    $today = Carbon::now();
    $startOfWeek = $today->startOfWeek(); // เริ่มที่วันจันทร์ของสัปดาห์ปัจจุบัน

    foreach ($selectedSlots as $slotData) {
        list($day, $slot) = $slotData;

        if (!isset($daysMap[$day])) {
            continue;
        }

        for ($i = 0; $i < $weeks; $i++) {
            $bookingDate = Carbon::parse($startOfWeek)->addWeeks($i)->startOfWeek()->addDays(array_search($day, array_keys($daysMap)))->toDateString();

            // ตรวจสอบว่ามีการจองแล้วหรือยัง
            $existingBooking = Booking::where('room_id', $roomId)
                ->where('day', $day)
                ->where('slot', $slot)
                ->where('booking_date', $bookingDate)
                ->exists();

            if (!$existingBooking) {
                Booking::create([
                    'room_id' => $roomId,
                    'name' => $name,
                    'reason' => $reason,
                    'day' => $day,
                    'slot' => $slot,
                    'booking_date' => $bookingDate,
                    'user_id' => auth()->user()->id, // เพิ่ม user_id ที่นี่
                ]);
            }
        }
    }
}

 // แสดงรายการการจองของผู้ใช้
 public function index()
 {
     $user = auth()->user();  // ดึงข้อมูลผู้ใช้ที่ล็อกอินอยู่
     $bookings = $user->bookings;  // ดึงรายการการจองทั้งหมดของผู้ใช้

     return view('bookings.index', compact('bookings'));
 }


 // ใน BookingController.php
public function cancelBooking($bookingId)
{
    $booking = Booking::find($bookingId);

    // ตรวจสอบว่าผู้ใช้เป็นเจ้าของการจองหรือไม่
    if ($booking && $booking->user_id == auth()->id()) {
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'การจองของคุณถูกยกเลิกเรียบร้อย');
    }

    return redirect()->route('bookings.index')->with('error', 'ไม่สามารถยกเลิกการจองนี้ได้');
}


}
