<?php

namespace App\Http\Controllers\Auth;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FcmService; // استيراد الخدمة
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }


    public function createOrder(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        $product = Product::find($request->product_id);
    
        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'Not enough stock available'], 400);
        }
    
        $total_price = $product->price * $request->quantity;
    
        $order = Order::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $total_price,
            'status' => 'pending',
        ]);
    
        $product->quantity -= $request->quantity;
        $product->save();
    
        return response()->json(['order' => $order], 201);
    }
    



    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'store_id' => 'required|exists:stores,id',
                'quantity' => 'required|integer|min:0',
            ]);
    
            $store = Store::findOrFail($request->store_id);
    
            // التحقق من البيانات الواردة
            \Log::info('Request Data:', $request->all());
    
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'store_id' => $store->id,
            ]);
    
            return response()->json(['message' => 'Product created successfully!', 'product' => $product], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating product:', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function update(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        $order->update($request->all());
    
        return response()->json($order);
    }
    
    public function destroy(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        $product = Product::find($order->product_id);
    
        if ($product) {
            $product->quantity += $order->quantity;
            $product->save();
        }
    
        $order->delete();
    
        return response()->json(null, 204);
    }
    







    public function notifyUsers(Product $product)
    {
        $title = 'New Product Added';
        $body = 'A new product "' . $product->name . '" has been added!';

        $users = User::whereNotNull('fcm_token')->get();

        foreach ($users as $user) {
            $this->fcmService->sendNotification(
                $user->fcm_token,
                $title,
                $body,
                ['product_id' => $product->id]
            );
        }
    }






    public function index()
    {
        // إرجاع قائمة المنتجات مع تفاصيل المتجر
        return Product::with('store')->get();
    }

    public function show(Product $product)
    {
        // إرجاع منتج واحد مع تفاصيل المتجر
        return $product->load('store');
    }


    public function search(Request $request)
    {
        $query = $request->input('query'); // استلام الكلمة المفتاحية من المستخدم

        // البحث عن المنتجات بناءً على الاسم أو الوصف
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();

        // البحث عن المتاجر بناءً على الاسم  
        $stores = Store::where('name', 'like', "%{$query}%")
            ->get();

        // إرجاع النتائج كاستجابة JSON
        return response()->json([
            'products' => $products,
            'stores' => $stores,
        ]);
    }



    //إرسال إشعار عند إضافة منتج إلى السلة:

    public function sendCartNotification(Product $product)
{
    $user = Auth::user();

    if (!$user || !$user->fcm_token) {
        return response()->json(['message' => 'No valid FCM token found'], 400);
    }

    $title = 'Item Added to Cart';
    $body = 'You have added "' . $product->name . '" to your cart.';

    $this->fcmService->sendNotification(
        $user->fcm_token,
        $title,
        $body,
        ['product_id' => $product->id]
    );

    return response()->json(['message' => 'Notification sent successfully']);
}





}
