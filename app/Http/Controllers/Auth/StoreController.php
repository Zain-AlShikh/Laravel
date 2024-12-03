<?php

namespace App\Http\Controllers\Auth;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function store(Request $request)
    {
        try {
            // التحقق من البيانات المرسلة
            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048', // التحقق من نوع الصورة
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
    
            // رفع الصورة إذا كانت موجودة
            $imagePath = $request->file('image') ? $request->file('image')->store('stores') : null;
    
            // إنشاء المتجر وتخزينه في قاعدة البيانات
            $store = Store::create([
                'name' => $request->name,
                'location' => $request->location,
                'image' => $imagePath,
            ]);

            // إذا كانت الصورة موجودة، نرجع رابط الصورة
            if ($store->image) {
                $store->image = Storage::url($store->image); // إرجاع الرابط الكامل للصورة
            }

            return response()->json(['store' => $store, 'message' => 'Store created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index()
    {
        $stores = Store::all();
        // إرجاع المتاجر مع روابط الصور
        foreach ($stores as $store) {
            if ($store->image) {
                $store->image = Storage::url($store->image); // إرجاع رابط الصورة إذا كانت موجودة
            }
        }
        return response()->json($stores); // إرجاع جميع المتاجر مع رابط الصورة
    }

    public function show(Store $store)
    {
        if ($store->image) {
            $store->image = Storage::url($store->image); // إرجاع رابط الصورة إذا كانت موجودة
        }
        return response()->json($store); // إرجاع متجر واحد مع رابط الصورة
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
