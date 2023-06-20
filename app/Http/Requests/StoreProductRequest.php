<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique('products', 'name')->where('tenant_id', tenant()->getKey())],
            'description' => ['nullable', 'string'],
            'product_type_id' => ['required', Rule::exists('product_types', 'id')],
            'visibility' => ['required', 'in:' . implode(',', array_values((array) Product::VISIBILITY_TYPE['Everyone']))],
            'advertised_price' => ['required', 'integer'],
            'status' => ['required', 'string'],
            'code' => ['required', 'string'],
            'terms_and_conditions' => ['nullable', 'string'],
            'mediaFiles' => ['nullable', 'array'],
            'mediaFiles.*' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'categories' => ['nullable', 'array', 'exists:categories,id'],
        ];
    }
}
