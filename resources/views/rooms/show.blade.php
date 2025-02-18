@extends('layouts.app')

@php
    use Carbon\Carbon;

    Carbon::setLocale('th');
    // ดึงค่า weekOffset และ weeks จาก request
    $weekOffset = intval(request()->input('week', 0));
    $weeks = intval(request()->input('weeks', 1)); // จำนวนสัปดาห์ที่เลือก

    $startOfWeek = Carbon::now('Asia/Bangkok')->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
    $endOfWeek = $startOfWeek->copy()->endOfWeek();

    // สร้างอาร์เรย์สำหรับหลายสัปดาห์
    $weeksRange = [];
    for ($i = 0; $i < $weeks; $i++) {
        $startOfWeekForCurrent = $startOfWeek->copy()->addWeeks($i);
        $weeksRange[] = [
            'start' => $startOfWeekForCurrent->toDateString(),
            'end' => $startOfWeekForCurrent->copy()->endOfWeek()->toDateString(),
        ];
    }

    // สร้างวันที่แต่ละวันในสัปดาห์
    $daysOfWeek = [
        'จันทร์' => $startOfWeek->copy()->toDateString(),
        'อังคาร' => $startOfWeek->copy()->addDay()->toDateString(),
        'พุธ' => $startOfWeek->copy()->addDays(2)->toDateString(),
        'พฤหัสบดี' => $startOfWeek->copy()->addDays(3)->toDateString(),
        'ศุกร์' => $startOfWeek->copy()->addDays(4)->toDateString(),
        'เสาร์' => $startOfWeek->copy()->addDays(5)->toDateString(),
        'อาทิตย์' => $startOfWeek->copy()->addDays(6)->toDateString(),
    ];

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

    $selectedSlots = request()->has('slots') ? json_decode(request()->input('slots'), true) : [];
@endphp

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">ตารางเรียนของ {{ $room->name }}</h1>

        <!-- เลื่อนสัปดาห์ -->
        <div class="text-center mb-3">
            <a href="{{ route('rooms.show', ['room' => $room->id, 'week' => $weekOffset - 1, 'slots' => json_encode($selectedSlots)]) }}"
                class="btn btn-outline-primary btn-lg">
                <i class="fa fa-arrow-left"></i> สัปดาห์ที่แล้ว
            </a>
            <span class="mx-4 text-bold" style="font-size: 1.25rem;">
                {{ $startOfWeek->isoFormat('D MMMM YYYY') }} ถึง {{ $endOfWeek->isoFormat('D MMMM YYYY') }}
            </span>
            <a href="{{ route('rooms.show', ['room' => $room->id, 'week' => $weekOffset + 1, 'slots' => json_encode($selectedSlots)]) }}"
                class="btn btn-outline-primary btn-lg">
                สัปดาห์ถัดไป <i class="fa fa-arrow-right"></i>
            </a>
        </div>

        <form action="{{ route('rooms.confirmBooking', ['room' => $room->id]) }}" method="GET">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
            <input type="hidden" id="selectedSlots" name="slots" value="{{ json_encode($selectedSlots) }}">
            <input type="hidden" name="weekOffset" value="{{ $weekOffset }}">
            <input type="hidden" name="weeks" value="{{ $weeks }}">

            <button type="submit" class="btn btn-success mt-3 btn-lg w-100">ยืนยันการเลือก</button>
        </form>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>วัน/เวลา</th>
                        @for ($i = 1; $i <= 12; $i++)
                            <th>คาบ {{ $i }} <br> <small>{{ $timeSlots[$i] }}</small></th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weeksRange as $weekIndex => $week)
                        @foreach (['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์', 'อาทิตย์'] as $day)
                            @php
                                // คำนวณวันที่ของวันในแต่ละสัปดาห์
                                $currentDate = \Carbon\Carbon::parse($week['start'])->addDays(
                                    array_search($day, [
                                        'จันทร์',
                                        'อังคาร',
                                        'พุธ',
                                        'พฤหัสบดี',
                                        'ศุกร์',
                                        'เสาร์',
                                        'อาทิตย์',
                                    ]),
                                );
                                // ตรวจสอบถ้าวันที่คำนวณได้ยังไม่ตรงกับวันที่ที่ต้องการ ให้ปรับเพิ่มวัน
                                if ($day == 'พฤหัสบดี' && $currentDate->format('d') == '19') {
                                    $currentDate->addDay();
                                }
                            @endphp
                            <tr>
                                <td class="align-middle">
                                    <strong>{{ $day }}</strong><br>
                                    <small>{{ $currentDate->format('d/m/Y') }}</small>
                                </td>
                                @for ($i = 1; $i <= 12; $i++)
                                    @php
                                        $booking = $bookings->first(function ($b) use ($currentDate, $i) {
                                            return Carbon::parse($b->booking_date)->isSameDay($currentDate) &&
                                                $b->slot == $i;
                                        });
                                        $slotKey = "{$day}_{$i}_{$weekIndex}";
                                    @endphp
                                    <td class="slot-cell {{ !$booking ? 'available' : 'booked' }} {{ in_array($slotKey, $selectedSlots) ? 'selected' : '' }}"
                                        data-day="{{ $day }}" data-slot="{{ $i }}"
                                        data-week="{{ $weekIndex }}">
                                        @if ($booking)
                                            <span class="text-danger"><strong>{{ $booking->name }}</strong>
                                                ({{ $booking->reason }})
                                            </span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    @endforeach


                </tbody>
            </table>
        </div>
    </div>

    <!-- เพิ่มที่ส่วน <head> หรือ ก่อนปิด </body> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '{{ session('error') }}',
                showConfirmButton: true
            });
        </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let selectedSlots = {!! json_encode($selectedSlots) !!};

            document.querySelectorAll(".slot-cell.available").forEach(cell => {
                let day = cell.getAttribute("data-day");
                let slot = cell.getAttribute("data-slot");
                let slotKey = `${day}_${slot}`;

                if (selectedSlots.includes(slotKey)) {
                    cell.classList.add("selected");
                }

                cell.addEventListener("click", function() {
                    if (this.classList.contains("selected")) {
                        this.classList.remove("selected");
                        selectedSlots = selectedSlots.filter(s => s !== slotKey);
                    } else {
                        this.classList.add("selected");
                        selectedSlots.push(slotKey);
                    }

                    document.getElementById("selectedSlots").value = JSON.stringify(selectedSlots);
                });
            });
        });
    </script>

    <style>
        .available {
            background-color: #d4edda;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .available:hover {
            background-color: #28a745;
            color: white;
        }

        .available.selected {
            background-color: #28a745 !important;
            color: white;
            font-weight: bold;
        }

        .booked {
            background-color: #f8d7da;
            color: #721c24;
        }

        th,
        td {
            vertical-align: middle;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
@endsection
