<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainRegionalRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "language_iso2" => "string",
            "timezone_iso3"  => "string",
            "currency_iso3"  => "string",
            "order_number_format"  => ["string", "in:" . implode(',', config('app.formats.order_number'))],
            "order_number_prefix"  => "required_if:order_number_format,string",
            "date_format" => ["string", "in:" . implode(',', config('app.formats.date'))],
            "time_format" => ["string", "in:" . implode(',', config('app.formats.time'))],
        ];
    }
}
