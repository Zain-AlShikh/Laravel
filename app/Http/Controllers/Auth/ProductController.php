<?php

namespace App\Http\Controllers\Auth;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ProductController extends Controller
{
    public function index()
    {
        return Product::all(); // إرجاع جميع المنتجات
    }

    public function show(Product $product)
    {
        return $product; // إرجاع منتج واحد
    }


    public function search(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('name', 'like', "%{$query}%")->get();
        $stores = Store::where('name', 'like', "%{$query}%")->get();
        return response()->json(['products' => $products, 'stores' => $stores]);
    }
    
}
