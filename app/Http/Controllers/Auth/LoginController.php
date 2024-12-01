<?php



namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        // التحقق من وجود المستخدم
        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'رقم الهاتف أو كلمة المرور غير صحيحة.'], 401);
        }

        // إنشاء التوكين للمستخدم
        $token = $user->createToken('YourAppName')->plainTextToken;

        // إعادة بيانات المستخدم مع التوكين
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح!',
            'user' => $user,
            'token' => $token,  // التوكين المولد
        ], 200);
    }


   
}
