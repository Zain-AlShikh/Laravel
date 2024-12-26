<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
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

    // إضافة تابع لتسجيل الخروج
    public function logout(Request $request)
    {
        // إبطال التوكن المستخدم
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful!',
        ], 200);
    }
}
