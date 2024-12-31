<?php

namespace App\Http\Controllers\Auth;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class StoreController extends Controller
{
    public function store(Request $request)
    {
        try {
            // التحقق من البيانات المرسلة
            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg', // إضافة نوع الملف وحجم الصورة المسموح به
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
    
            // إذا كانت الصورة موجودة
            $imagePath = null;
            if ($request->hasFile('image')) {
                // تخزين الصورة في مجلد 'stores' داخل storage/app/public
                $imagePath = $request->file('image')->store('stores', 'public'); // تخزين الصورة في مجلد 'stores' داخل storage/app/public
            }
    
            // إنشاء المتجر
            $store = Store::create([
                'name' => $request->name,
                'location' => $request->location,
                'image' => $imagePath,
            ]);
    
            return response()->json(['store' => $store, 'message' => 'Store created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }



    public function index()
    {
        $stores = Store::all();
        foreach ($stores as $store) {
            if ($store->image) {
                $store->image = asset('storage/' . $store->image);  // إرجاع رابط الصورة إذا كانت موجودة
            }
        }
        return response()->json(['stores' => $stores]); 
    }

    public function show(Store $store)
    {
     
        if ($store->image) {
            $store->image = asset('storage/' . $store->image); // توليد رابط URL للصورة
        }
        return response()->json(['store' => $store] ); // إرجاع بيانات المتجر مع رابط الصورة
    }

    public function search(Request $request)
    {
        $query = $request->input('query'); // استلام الكلمة المفتاحية من المستخدم


        // البحث عن المتاجر بناءً على الاسم  
        $stores = Store::where('name', 'like', "%{$query}%")
            ->get();

        // إرجاع النتائج كاستجابة JSON مع رابط الصورة للمتاجر
        foreach ($stores as $store) {
            if ($store->image) {
                $store->image = Storage::url($store->image); // إرجاع رابط الصورة للمتاجر
            }
        }

        // إرجاع المنتجات والمتاجر
        return response()->json([
            'stores' => $stores,
        ]);
    }
}
