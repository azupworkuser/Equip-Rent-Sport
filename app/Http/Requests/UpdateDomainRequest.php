<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PragmaRX\Countries\Package\Countries;

class UpdateDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return tenant()->isInAdminTeam(auth()->id());
    }

    public function rules(): array
    {
        return [
            'location_name' => 'required|string|unique:domains,location_name,' . $this->domain->getKey(),
            "company" => "required|string|max:255",
            "website" => "string|nullable",
            "primary_industry" => "in:" . implode(',', config('app.primary_industry')),
            "phone" => "required|string|max:255",
            "dial_code" => "required|string|max:255",
            "company_description" => "string|nullable",
            "address_1" => "required|string|max:255",
            "address_2" => "string|max:255",
            "city" => "string",
            "state" => "string",
            "zip" => "string",
            "country" => ["required", "string", function ($attribute, $value, $fail) {
                if (! (new Countries())->where('cca2', $value)->first()->count()) {
                    $fail('The selected country code is invalid. Please use 2 digit country iso code');
                }
            }],
        ];
    }
}
