<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
class Register extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payload.name' => "required",
            'payload.email' => ['required', 'string', 'lowercase', 'email', 'max:255','unique:users,email'],
            'payload.password' => ['required', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'payload.name.required' => "Name is required, Please fill out the required field!",
            'payload.email.required' => "Email is required, Please fill out the required field!",
            'payload.email.string' => "The Email address must be a valid string.",
            'payload.email.lowercase' => "Lower case required! for email address",
            'payload.email.email' => "The email address must be a valid email",
            'payload.email.max' => "The Email address may not be greater than 255 characters.",
            'payload.password.required' => "Password is required, Please fill out the required field!",
            'payload.password.confirmed' => "Password confirmation does not match.!",
        ];
    }
}
