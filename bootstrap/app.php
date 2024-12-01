<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // تأكد من إضافة الـ Middleware الضروري لعمل Sanctum في API
        $middleware->api(prepend: [
            \Illuminate\Session\Middleware\StartSession::class, // بدء الجلسة إذا كان هناك حاجة لها (مثل استخدام الكوكيز)
            // تم إزالة auth:sanctum لأنه غير مطلوب الآن
        ]);

        // يمكنك إضافة Middleware أخرى مثل التحقق من البريد الإلكتروني (إذا كان لديك متطلبات خاصة)
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class, // التحقق من بريد المستخدم
        ]);

        // أضف المزيد من الـ Middleware هنا إذا لزم الأمر
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
