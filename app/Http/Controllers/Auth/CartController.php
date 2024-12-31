<?php

namespace App\Http\Controllers\Auth;

use App\Services\FcmService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class CartController extends Controller
{
    // عرض جميع العناصر في السلة
    public function index()
    {
        
    $carts = DB::table('carts')->select('id', 'user_id', 'product_id', 'quantity', 'created_at', 'updated_at')->get();

    // حساب المجموع الكلي للأسعار
    $totalPrice = DB::table('carts')
        ->join('products', 'carts.product_id', '=', 'products.id')
        ->select(DB::raw('SUM(products.price * carts.quantity) as total_price'))
        ->value('total_price');

 
    $formattedTotalPrice = number_format($totalPrice, 2) . '$';

    
    return response()->json([
        'carts' => $carts,
        'total_price' => $formattedTotalPrice,
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
    private function calculateTotalPrice($userId)
    {
        $totalPrice = DB::table('carts')
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->where('carts.user_id', $userId)
            ->select(DB::raw('SUM(products.price * carts.quantity) as total_price'))
            ->value('total_price');

        return $totalPrice;
    }



    // التابع الذي يحفظ الطلب في جدول 'orders'
    public function storeOrder(Request $request)
    {
        // تأكد من أن المستخدم قد سجل الدخول
        if (Auth::guest()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        // حساب المجموع الكلي للأسعار من السلة
        $totalPrice = DB::table('carts')
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(products.price * carts.quantity) as total_price'))
            ->where('carts.user_id', Auth::id()) // التأكد من أن السلة تخص المستخدم
            ->value('total_price');
    
        // إذا كان هناك خطأ في الحساب أو السلة فارغة
        if ($totalPrice === null) {
            return response()->json(['error' => 'No items in cart'], 400);
        }
    
        // تنسيق السعر النهائي بدون الفواصل
        $formattedTotalPrice = number_format($totalPrice, 2, '.', ''); // لا نضيف الفواصل
    
        // جلب الموقع من جدول users باستخدام id المستخدم
        $user = Auth::user();
        $location = $user->location; // تأكد من أن العمود location موجود في جدول users
        
        if (!$location) {
            return response()->json(['error' => 'Location not found'], 400);
        }
    
        // إضافة طلب جديد في جدول orders
        DB::table('orders')->insert([
            'user_id' => Auth::id(), // أو استخدم طريقة أخرى للحصول على ID المستخدم
            'location' => $location, // استخدام الموقع من جدول users
            'final_price' => $formattedTotalPrice, // تخزين السعر النهائي
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // إرجاع استجابة تحتوي على البيانات
        return response()->json([
            'message' => 'Order placed successfully',
            'total_price' => $formattedTotalPrice . '$', // إضافة رمز الدولار هنا عند الإرجاع
        ]);
    }
    
    




// جلب جميع الطلبات من جدول orders
public function getAllOrders()
{
    // جلب الطلبات من جدول orders مع ترتيبها تنازليًا بناءً على وقت الإنشاء
    $orders = DB::table('orders')
        ->select('id', 'user_id', 'location', 'final_price', 'created_at', 'updated_at')
        ->orderBy('created_at', 'desc')
        ->get();

    // التحقق إذا كان جدول الطلبات فارغًا
    if ($orders->isEmpty()) {
        return response()->json(['message' => 'No orders found'], 404);
    }

    // إرجاع الطلبات في استجابة JSON
    return response()->json([
        'message' => 'Orders retrieved successfully',
        'orders' => $orders,
    ], 200);
}

}


