<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'email', // البريد الإلكتروني
        'password', // كلمة المرور
    ];

    // يمكنك إضافة علاقات مستقبلية إذا كان هناك علاقة بين Driver وجداول أخرى.
}
