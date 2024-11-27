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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient product quantity'], 400);
        }

        $totalPrice = $product->price * $request->quantity;

        $order = Order::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // تحديث الكمية في المنتج
        $product->quantity -= $request->quantity;
        $product->save();

        return response()->json($order, 201);
    }

   



    
    public function destroy(Order $order)
    {
        // إعادة الكمية إلى المنتج قبل حذف الطلب
        $product = $order->product;
        $product->quantity += $order->quantity;
        $product->save();

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }



    
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,delivered,cancelled',
        ]);

        $product = $order->product;

        // تحديث الكمية إذا تغيرت
        if ($request->quantity != $order->quantity) {
            $product->quantity += $order->quantity; // إعادة الكمية السابقة
            if ($product->quantity < $request->quantity) {
                return response()->json(['message' => 'Insufficient product quantity'], 400);
            }
            $product->quantity -= $request->quantity; // تقليل الكمية الجديدة
            $product->save();
        }

        $order->update([
            'quantity' => $request->quantity,
            'status' => $request->status,
            'total_price' => $product->price * $request->quantity,
        ]);

        return response()->json($order);
    }

}
