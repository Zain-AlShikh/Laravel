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
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'phone.required' => 'The phone number is required.',
            'phone.regex' => 'The phone number must be 10 digits.',
            'phone.unique' => 'The phone number is already registered.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'location.required' => 'The location is required.',
            'profile_image.image' => 'The file must be an image.',
            'profile_image.mimes' => 'Supported image formats are: jpg, jpeg, png, gif.',
            'profile_image.max' => 'The image size must not exceed 2MB.',
        ];
    }
}