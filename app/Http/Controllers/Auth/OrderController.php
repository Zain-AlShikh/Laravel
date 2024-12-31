<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // جلب الموقع من المستخدم
        $user = User::find($request->user_id);
        $location = $user->location;

        // حساب السعر النهائي من جدول cart
        $finalPrice = Cart::where('user_id', $request->user_id)->sum('final_price');

        // حفظ الطلب
        $order = new Order();
        $order->user_id = $request->user_id;
        $order->location = $location;
        $order->final_price = $finalPrice;
        $order->save();

        return response()->json([
            'message' => 'Order placed successfully!',
            'order' => $order,
        ], 200);
    }
}
