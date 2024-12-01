<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); 
        $carts = Cart::where('user_id', $userId)->with('product')->get();
        return response()->json($carts);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $cart = Cart::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);
        return response()->json($cart, 201);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return response()->json(null, 204);
    }
}
