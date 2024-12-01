<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'profile_image',
        'location',
        'email',
        'password',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


        /**
     * رفع الصورة وتخزين المسار في قاعدة البيانات
     */
    public function setProfileImageAttribute($value)
    {
        if (is_file($value)) {
            // تخزين الصورة في مجلد 'profile_images' ضمن مجلد التخزين العام
            $this->attributes['profile_image'] = $value->store('profile_images', 'public');
        }
    }

    /**
     * إرجاع الرابط العام للصورة في الواجهة
     */
    public function getProfileImageUrlAttribute()
    {
        return Storage::url($this->profile_image);
    }

}
