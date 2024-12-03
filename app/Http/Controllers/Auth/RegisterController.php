<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'fcm_token' => $request->fcm_token,
        ];
    
        if ($request->hasFile('profile_image')) {
            $userData['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }
    
        // إنشاء المستخدم
        $user = User::create($userData);
    
        // إنشاء التوكين
        $token = $user->createToken('YourAppName')->plainTextToken;
    
        return response()->json([
            'message' => 'Registration successful!',
            'user' => $user,
            'token' => $token,
            'profile_image_url' => $user->profile_image ? Storage::url($user->profile_image) : null,
        ], 201);
    }
    
}
