<?php

namespace App\Http\Controllers\Auth;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class StoreController extends Controller
{
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
    $query = $request->input('query');
    $products = Product::where('name', 'like', "%{$query}%")->get();
    $stores = Store::where('name', 'like', "%{$query}%")->get();
    return response()->json(['products' => $products, 'stores' => $stores]);
}

}
