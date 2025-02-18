<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // แสดงหน้า Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ประมวลผลการ Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->route('rooms.index')->with('success', 'เข้าสู่ระบบสำเร็จ');
        }

        return back()->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง']);
    }

    // ประมวลผลการ Logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'ออกจากระบบแล้ว');
    }
    // แสดงฟอร์มสมัครสมาชิก
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // สมัครสมาชิก
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ]);

    // สร้าง User ใหม่
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // ล็อกอินอัตโนมัติหลังสมัคร
    Auth::login($user);

    // เปลี่ยนเส้นทางไปที่หน้า rooms หลังจากสมัครเสร็จ
    return redirect()->route('rooms.index')->with('success', 'สมัครสมาชิกสำเร็จ');
}

}
