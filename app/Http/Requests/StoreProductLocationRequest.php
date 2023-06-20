<?php

namespace App\Http\Requests;

use App\Models\ProductLocation;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductLocationRequest extends FormRequest
{
    use FormRequestHelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->tokenCan('inventory.product.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'address_1' => 'required|string',
            'address_2' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'map_link' => 'nullable|string',
            'address_type' => 'required|string|in:' . implode(',', ProductLocation::ADDRESS_TYPE),
        ];
    }
}
