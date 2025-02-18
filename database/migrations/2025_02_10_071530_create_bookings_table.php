<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // ลบ nullable() ออก
            $table->date('booking_date');
            $table->string('name');
            $table->string('reason');
            $table->string('day');  // เก็บวัน เช่น "จันทร์", "อังคาร"
            $table->integer('slot'); // คาบเรียนที่จอง
            $table->integer('weeks')->default(1); // เก็บจำนวนสัปดาห์ที่เลือก
            $table->timestamps();

            // กำหนด Foreign Key สำหรับ room_id และ user_id
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // ป้องกันการจองซ้ำ (ห้องเดียวกัน, วันเดียวกัน, คาบเดียวกัน)
            $table->unique(['room_id', 'booking_date', 'slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

