@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center">ยืนยันการจองห้อง {{ $room->name }}</h1>

        <div class="my-3">
            <h3>ช่วงเวลาที่เลือก:</h3>
            <ul>
                @php
                    use Carbon\Carbon;

                    // ดึงค่า weekOffset จาก request และแปลงเป็น int
                    $weekOffset = intval(request()->input('weekOffset', 0));

                    // คำนวณวันเริ่มต้นของสัปดาห์ตาม weekOffset
                    $startOfWeek = Carbon::now()->startOfWeek()->addWeeks($weekOffset);

                    // Map ชื่อวันเป็น index ของ Carbon
                    $daysMap = [
                        'จันทร์' => 0,
                        'อังคาร' => 1,
                        'พุธ' => 2,
                        'พฤหัสบดี' => 3,
                        'ศุกร์' => 4,
                        'เสาร์' => 5,
                        'อาทิตย์' => 6,
                    ];
                @endphp

                @foreach ($selectedSlots as $slotData)
                    @php
                        [$day, $slot] = explode('_', $slotData);

                        if (!isset($daysMap[$day])) {
                            continue;
                        }

                        // ใช้ weekOffset ในการคำนวณสัปดาห์ที่ถูกต้อง
                        $dayOffset = intval($daysMap[$day]);
                        $date = $startOfWeek->copy()->addDays($dayOffset);

                        $timeSlots = [
                            1 => ['08:00', '09:00'],
                            2 => ['09:00', '10:00'],
                            3 => ['10:10', '11:10'],
                            4 => ['11:10', '12:10'],
                            5 => ['12:10', '13:00'],
                            6 => ['13:00', '14:00'],
                            7 => ['14:00', '15:00'],
                            8 => ['15:10', '16:10'],
                            9 => ['16:10', '17:10'],
                            10 => ['17:30', '18:30'],
                            11 => ['18:30', '19:30'],
                            12 => ['19:30', '20:30'],
                        ];

                        $startTime = $timeSlots[$slot][0];
                        $endTime = $timeSlots[$slot][1];

                        // แสดงวันที่ที่คำนวณได้
                        $dateFormatted = $date->locale('th')->isoFormat('dddd ที่ D MMMM YYYY');
                        
                        // เพิ่มวันที่คำนวณลงใน bookingDates
                        $bookingDates[] = $date->toDateString();
                    @endphp

                    <li>
                        <strong>{{ $day }}:</strong>
                        <p>{{ $dateFormatted }} เวลา {{ $startTime }} - {{ $endTime }}</p>
                    </li>
                @endforeach





            </ul>
        </div>

        <form action="{{ route('rooms.storeBooking', ['room' => $room->id]) }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
            <input type="hidden" name="selected_slots" value="{{ json_encode($selectedSlots) }}">
            <input type="hidden" name="week" value="{{ $weekOffset }}">

            <input type="hidden" name="booking_dates" value="{{ json_encode($bookingDates) }}">
            
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อผู้จอง</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">ชื่อวิชา</label>
                <textarea name="reason" id="reason" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label for="weeks" class="form-label">จำนวนสัปดาห์ที่จอง</label>
                <select name="weeks" id="weeks" class="form-control">
                    <option value="1">1 สัปดาห์</option>
                    <option value="2">2 สัปดาห์</option>
                    <option value="3">3 สัปดาห์</option>
                    <option value="4">4 สัปดาห์</option>
                </select>
            </div>

            
    <button type="button" class="btn btn-success" onclick="confirmBooking()">ยืนยันการจอง</button>
            <a href="{{ route('rooms.show', ['room' => $room->id, 'week' => $weekOffset]) }}"
                class="btn btn-secondary">ย้อนกลับ</a>
        </form>
    </div>


    <script>
        function confirmBooking() {
            // ใช้ confirm() เพื่อถามยืนยัน
            if (confirm("คุณแน่ใจที่จะจองห้องนี้ใช่หรือไม่?")) {
                // ถ้ายืนยันให้ส่งฟอร์ม
                document.getElementById("bookingForm").submit();
            } else {
                // ถ้ายกเลิกไม่ทำอะไร
                console.log("การจองถูกยกเลิก");
            }
        }
    </script>

@endsection
