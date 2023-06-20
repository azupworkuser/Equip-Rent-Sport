<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCustomerRequest extends FormRequest
{
    use FormRequestHelperTrait;

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
                "first_name" => "required|min:2|max:255",
                "last_name" => "required|min:2|max:255",
                "email" => "required|email",
                "phone" => "required|string|max:255",
                "phone_code" => "required|string|max:255",
                "address1" => "nullable|string|max:255",
                "address2" => "nullable|string|max:255",
                "city" => "nullable|string|max:255",
                "state" => "nullable|string|max:255",
                "country" => "nullable|string|max:255",
                "zip_code" => "nullable|string|max:255",
            ];
    }
}
