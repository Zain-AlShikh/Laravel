<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // معرف المستخدم
        'location', // الموقع
        'final_price', // السعر النهائي
    ];

    /**
     * تعريف العلاقة بين الطلب والمستخدم.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
