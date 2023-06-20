<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterStep1Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'required|min:2|max:255',
            'domain' => 'required|unique:domains',
            'password' => ['required', Password::min(6)->mixedCase()->numbers()->symbols()->uncompromised()],
            'email' => 'required|email|unique:users',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'This email address is already in use. To register new business please login and create new business under your account.',
        ];
    }
}
