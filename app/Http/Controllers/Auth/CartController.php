<?php

namespace App\Http\Controllers\Auth;

use App\Services\FcmService; // استيراد الخدمة
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // إرجاع جميع العناصر في السلة مع تفاصيل المنتج
        $carts = Cart::where('user_id', $userId)->with('product')->get();

        return response()->json($carts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $product = Product::find($request->product_id);

        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient product quantity'], 400);
        }

        $cart = Cart::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        // إرسال الإشعار
        $this->sendCartNotification($product);

        return response()->json($cart, 201);
    }

    public function sendCartNotification(Product $product)
    {
        $fcmService = app(FcmService::class); // الحصول على الخدمة

        $title = 'Item Added to Cart';
        $body = 'You have added "' . $product->name . '" to your cart.';

        $user = Auth::user();

        if ($user->fcm_token) {
            $fcmService->sendNotification(
                $user->fcm_token,
                $title,
                $body,
                ['product_id' => $product->id]
            );
        }
    }


    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json(['message' => 'Cart item deleted successfully'], 204);
    }
}
