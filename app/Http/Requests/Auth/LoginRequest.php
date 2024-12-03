<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'password' => 'required|string',
            // 'fcm_token' => 'required|string',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'The phone number is required.',
            'phone.regex' => 'The phone number must be 10 digits.',
            'password.required' => 'The password is required.',
            // 'fcm_token.required' => 'The FCM token is required.',
        ];
    }
}
