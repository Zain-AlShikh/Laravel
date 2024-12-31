<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Driver;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The phone number or password is incorrect.'], 401);
        }

        // تحديث fcm_token
        $user->fcm_token = $request->fcm_token;
        $user->save();

        // إنشاء التوكين
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function driverLogin(Request $request)
    {
       // تحقق من صحة المدخلات
    $request->validate([
        'email' => 'required|email|unique:drivers,email',
        'password' => 'required|min:8',
    ]);

    // إنشاء سائق جديد وتخزين بياناته
    $driver = Driver::create([
        'email' => $request->email,
        'password' => Hash::make($request->password), // تخزين كلمة المرور مشفرة
    ]);

    // إنشاء التوكن للسائق بعد التسجيل
    $token = $driver->createToken('DriverApp')->plainTextToken;

    return response()->json([
        'message' => 'Driver registration successful!',
        'driver' => $driver,
        'token' => $token,
    ], 201);
    }
    

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful!',
        ], 200);
    }
}
