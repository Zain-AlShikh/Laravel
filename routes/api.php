<?php

use App\Http\Controllers\Auth\CartController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\StoreController;
use App\Http\Controllers\Auth\ProductController;
use App\Http\Controllers\Auth\OrderController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// مسارات غير محمية (التسجيل والدخول)
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// المسارات المحمية بمصادقة Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // مسارات المتاجر
    Route::post('/stores', [StoreController::class, 'store']);
    Route::get('/stores', [StoreController::class, 'index']);
    Route::get('/stores/{store}', [StoreController::class, 'show']);
    Route::post('/stores/search', [StoreController::class, 'search']);

    // مسارات المنتجات
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::post('/products/search', [ProductController::class, 'search']);

    // مسارات السلة (Cart)
    Route::get('/cart', [CartController::class, 'index']); // عرض محتويات السلة
    Route::post('/cart', [CartController::class, 'store']); // إضافة عنصر للسلة
    Route::put('/cart/{cart}', [CartController::class, 'update']); // تعديل عنصر في السلة
    Route::delete('/cart/{cart}', [CartController::class, 'destroy']); // حذف عنصر من السلة

    
});
