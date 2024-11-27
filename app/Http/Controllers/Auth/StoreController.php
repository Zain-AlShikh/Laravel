<?php

namespace App\Http\Controllers\Auth;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class StoreController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048',
            ]);
    
            // التحقق من وجود متجر بنفس الاسم والموقع
            $existingStore = Store::where('name', $request->name)
                ->where('location', $request->location)
                ->first();
    
            if ($existingStore) {
                return response()->json([
                    'error' => 'Store with the same name and location already exists.'
                ], 409); // رمز HTTP 409 يشير إلى وجود تضارب
            }
    
            // إنشاء المتجر الجديد
            $store = Store::create([
                'name' => $request->name,
                'location' => $request->location,
                'image' => $request->file('image') ? $request->file('image')->store('stores') : null,
            ]);
    
            return response()->json(['store' => $store, 'message' => 'Store created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    




    public function index()
    {
        return Store::all(); // إرجاع جميع المتاجر
    }

    public function show(Store $store)
    {
        return $store; // إرجاع متجر واحد
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

}
