@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center">รายการห้องเรียน</h1>

    @if($rooms->isEmpty())
        <p class="text-center">ยังไม่มีห้องเรียนในระบบ</p>
    @else
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach ($rooms as $room)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $room->name }}</h5>
                            <p class="card-text">รายละเอียดของห้องเรียน</p>

                            @auth
                                <!-- ถ้าผู้ใช้ล็อกอิน ให้แสดงปุ่ม "ดูรายละเอียด" -->
                                <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-primary">ดูรายละเอียด</a>
                            @else
                                <!-- ถ้ายังไม่ได้ล็อกอิน ให้พาไปที่หน้า Login -->
                                <a href="{{ route('login') }}" class="btn btn-warning">เข้าสู่ระบบเพื่อจองห้อง</a>
                            @endauth

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
