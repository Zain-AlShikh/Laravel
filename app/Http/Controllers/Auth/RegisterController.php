<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'location' => $request->location,
        ];
    
        // التحقق من وجود صورة وتحميلها إذا كانت موجودة
        if ($request->hasFile('profile_image')) {
            $userData['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }
    
        // إنشاء المستخدم
        $user = User::create($userData);
    
        // إنشاء التوكين للمستخدم
        $token = $user->createToken('YourAppName')->plainTextToken;
    
        // إعادة بيانات المستخدم مع التوكين
        return response()->json([
            'message' => 'Registration successful!',
            'user' => $user,
            'token' => $token,  
        ], 201);
    }
}
