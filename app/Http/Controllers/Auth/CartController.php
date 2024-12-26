<?php

namespace App\Http\Controllers\Auth;

use App\Services\FcmService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    // عرض جميع العناصر في السلة
    public function index()
    {
        $userId = Auth::id();

        // إرجاع جميع العناصر في السلة مع تفاصيل المنتج
        $carts = Cart::where('user_id', $userId)->with('product')->get();

        // حساب السعر الإجمالي
        $totalPrice = $this->calculateTotalPrice($carts);

        return response()->json([
            'carts' => $carts,
            'total_price' => $totalPrice . "$",
        ]);
    }

    // إضافة عنصر جديد إلى السلة
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        
        $userId = Auth::id();
        $product = Product::find($request->product_id);

        // تحقق من توفر الكمية المطلوبة
        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient product quantity'], 400);
        }

        // خصم الكمية من المنتج الرئيسي
        $product->quantity -= $request->quantity;
        $product->save();

        // إضافة المنتج إلى السلة
        $cart = Cart::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        // إرسال الإشعار
        $this->sendCartNotification($product);

        return response()->json($cart, 201);
    }

    // تعديل عنصر في السلة
    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($cart->product_id);

        // إعادة الكمية السابقة إلى المخزون
        $product->quantity += $cart->quantity;

        // التأكد من توفر الكمية الجديدة
        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient product quantity'], 400);
        }

        // تحديث الكمية في السلة
        $cart->update(['quantity' => $request->quantity]);

        // خصم الكمية الجديدة من المخزون
        $product->quantity -= $request->quantity;
        $product->save();
        return response()->json(['message' => 'Cart item updated successfully', 'cart' => $cart], 200);

    }

  // حذف عنصر من السلة
public function destroy(Cart $cart)
{
    $product = Product::find($cart->product_id);

    // إعادة الكمية إلى المخزون
    $product->quantity += $cart->quantity;
    $product->save();


    $taxRate = 0.10; // 10% ضريبة على المنتج المحذوف
    $productPrice = $product->price ?? 0;  // السعر الأصلي للمنتج
    $taxAmount = $productPrice * $taxRate;  // حساب الضريبة على المنتج

    // تحديث السعر النهائي بإضافة الضريبة
    $finalPriceWithTax = ($productPrice * $cart->quantity) + $taxAmount;

    // حذف العنصر من السلة
    $cart->delete();

    // إرسال رسالة مع السعر النهائي بعد إضافة الضريبة
    return response()->json([
        'message' => 'Cart item deleted successfully',
        'final_price_with_tax' => $finalPriceWithTax
    ], 204);
}


    // إرسال إشعار عند إضافة عنصر إلى السلة
    public function sendCartNotification(Product $product)
    {
        $fcmService = app(FcmService::class);

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

    // حساب السعر الإجمالي للمنتجات في السلة
    private function calculateTotalPrice($carts)
    {
        $totalPrice = 0;

        foreach ($carts as $cart) {
            $productPrice = $cart->product->price ?? 0;
            $totalPrice += $productPrice * $cart->quantity;
        }

        return $totalPrice;
    }
}
