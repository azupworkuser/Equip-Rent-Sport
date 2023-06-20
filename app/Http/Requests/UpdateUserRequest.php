<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'required|min:2|max:255',
            'domainRoles' => "required|array",
            'password' => ['sometimes', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required_if:password,!=, ""', Password::defaults()],
            'old_password' => ['required_if:password,!=, ""'],
        ];
    }
}
