<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'location' => 'required|string|max:255',
            // إضافة القاعدة للتحقق من الصورة
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'الاسم الأول مطلوب.',
            'last_name.required' => 'الاسم الأخير مطلوب.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.regex' => 'يجب أن يكون رقم الهاتف مكونًا من 10 أرقام.',
            'phone.unique' => 'رقم الهاتف مسجل بالفعل.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'يجب أن تكون كلمة المرور مكونة من 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
            'location.required' => 'الموقع مطلوب.',
            // رسائل خطأ الصورة
            'profile_image.image' => 'يجب أن يكون الملف صورة.',
            'profile_image.mimes' => 'الصور المدعومة فقط هي: jpg، jpeg، png، gif.',
            'profile_image.max' => 'يجب ألا تتجاوز الصورة 2 ميجابايت.',
        ];
    }
}
