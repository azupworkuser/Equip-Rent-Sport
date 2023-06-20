<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStep2Request extends FormRequest
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
            'primary_industry' => 'nullable|string',
            'timezone_iso3' => 'nullable|string',
            'phone' => 'nullable|string',
            'dial_code' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'required',
            'currency_iso3' => 'nullable|string',
            'website' => 'nullable|string',
            'company' => 'required|string',
            'location_name' => 'required|string|unique:domains,location_name,' . tenant()->primary_domain->getKey(),
        ];
    }
}
