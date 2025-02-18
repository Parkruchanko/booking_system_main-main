@extends('layouts.app')

@section('title', 'รายการการจองของฉัน')

@section('content')
    <h1>รายการการจองของฉัน</h1>

    @if($bookings->isEmpty())
        <p>คุณยังไม่ได้ทำการจองห้อง</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>ห้อง</th>
                    <th>วัน</th>
                    <th>คาบ</th>
                    <th>วันที่จอง</th>
                    <th>ชื่อผู้จอง</th>
                    <th>วิชา</th>
                    <th>ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $timeSlots = [
                        1 => '08:00-09:00',
                        2 => '09:00-10:00',
                        3 => '10:10-11:10',
                        4 => '11:10-12:10',
                        5 => '12:10-13:00',
                        6 => '13:00-14:00',
                        7 => '14:00-15:00',
                        8 => '15:10-16:10',
                        9 => '16:10-17:10',
                        10 => '17:30-18:30',
                        11 => '18:30-19:30',
                        12 => '19:30-20:30',
                    ];

                    // จัดกลุ่มการจองตามวัน
                    $groupedBookings = $bookings->groupBy('booking_date');
                @endphp

                @foreach($groupedBookings as $date => $groupBookings)
                    @php
                        $previousSlot = null;
                        $combinedSlotStart = null;
                        $combinedSlotEnd = null;
                        $combinedSlot = '';
                    @endphp

                    @foreach($groupBookings->sortBy('slot') as $index => $booking)
                        @php
                            $currentSlot = $booking->slot;
                            $currentTimeSlot = $timeSlots[$currentSlot] ?? 'ไม่ระบุเวลา';

                            // ตรวจสอบว่าเวลาของคาบปัจจุบันและคาบก่อนหน้านี้ติดกันหรือไม่
                            if ($previousSlot && $currentSlot == $previousSlot + 1) {
                                // คาบต่อเนื่องให้รวมเวลา
                                $combinedSlotEnd = $currentSlot;
                            } else {
                                // ถ้าคาบไม่ต่อเนื่องแสดงช่วงเวลาเดิม
                                if ($combinedSlotStart !== null) {
                                    // เช็คว่าระยะห่างระหว่างคาบเกินกว่าที่สามารถรวมได้หรือไม่
                                    $timeDifference = $currentSlot - $previousSlot;
                                    if ($timeDifference > 1) {
                                        // ถ้าห่างเกิน 1 คาบ (เช่น 09:00-10:00 กับ 12:10-13:00) จะไม่รวมกัน
                                        $combinedSlot = $timeSlots[$combinedSlotStart] . ' - ' . $timeSlots[$combinedSlotEnd];
                                    }
                                }
                                $combinedSlotStart = $currentSlot;
                                $combinedSlotEnd = $currentSlot;
                            }

                            $previousSlot = $currentSlot;
                        @endphp

                        <tr>
                            <td>{{ $booking->room->name ?? 'ห้องไม่พบ' }}</td>
                            <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                            <td>{{ $combinedSlot ?: $currentTimeSlot }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                            <td>{{ $booking->name }}</td>
                            <td>{{ $booking->reason }}</td>
                            <td>
                                @if($booking->user_id == auth()->id()) <!-- ตรวจสอบว่าเป็นเจ้าของการจอง -->
                                    <a href="{{ route('bookings.cancel', $booking->id) }}" class="btn btn-danger" onclick="return confirm('คุณต้องการยกเลิกการจองนี้?')">ยกเลิกการจอง</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @php
                        // ถ้ามีคาบที่ต่อเนื่องแล้วแสดงผลช่วงเวลา
                        if ($combinedSlotStart !== null) {
                            $combinedSlot = $timeSlots[$combinedSlotStart] . ' - ' . $timeSlots[$combinedSlotEnd];
                        }
                    @endphp
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
