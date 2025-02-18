<?php

use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ระบบลงทะเบียนและล็อกอิน
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// แสดงรายการห้องเรียน
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

// จองห้องเรียน (เฉพาะผู้ที่ล็อกอิน)
Route::middleware('auth')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}/cancel', [BookingController::class, 'cancelBooking'])->name('bookings.cancel'); // เพิ่มเส้นทางยกเลิก
    Route::get('/rooms/{room}/confirm-booking', [BookingController::class, 'confirmBooking'])->name('rooms.confirmBooking');
    Route::post('/rooms/{room}/book', [BookingController::class, 'storeBooking'])->name('rooms.storeBooking'); // เปลี่ยนชื่อ route ให้ชัดเจนขึ้น
});
