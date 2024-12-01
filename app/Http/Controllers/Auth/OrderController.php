<?php

namespace App\Http\Controllers\Auth;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        /**
         * @var \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard $auth
         */
        $auth = auth();

        if ($auth->check()) {
            return Order::where('user_id', $auth->id())->get();
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }

    public function store(Request $request)
    {
        // منطق إنشاء الطلب
        $order = Order::create($request->all());

        // تحديث الكمية بعد إتمام الطلب
        $product = Product::find($order->product_id);

        // تحقق إذا كان المنتج غير موجود
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->quantity -= $order->quantity; // تقليل الكمية
        $product->save();

        return response()->json($order, 201);
    }

    public function destroy(Order $order)
    {
        // استرجاع الكمية عند إلغاء الطلب
        $product = Product::find($order->product_id);

        // تحقق إذا كان المنتج موجود
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->quantity += $order->quantity; // إضافة الكمية مرة أخرى
        $product->save();

        // حذف الطلب
        $order->delete();

        return response()->json(null, 204);
    }

    public function update(Request $request, Order $order)
{
    $order->update($request->all());
    return response()->json($order);
}

}
