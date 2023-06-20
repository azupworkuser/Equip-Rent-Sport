<?php

namespace App\Http\Requests;

use App\Models\ProductInventory;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductInventoryRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'inventory_type' => 'required|string|in:' . ProductInventory::INVENTORY_TYPE_DYNAMIC . ',' . ProductInventory::INVENTORY_TYPE_UNLIMITED . ',' . ProductInventory::INVENTORY_TYPE_FIXED,
            'assets' => 'required_if:inventory_type,' . ProductInventory::INVENTORY_TYPE_DYNAMIC . '|array|min:1',
            'assets.*' => 'required_if:inventory_type,' . ProductInventory::INVENTORY_TYPE_DYNAMIC . '|string|exists:assets,id',
            'quantity' => 'required_if:inventory_type,' . ProductInventory::INVENTORY_TYPE_FIXED . '|integer|min:1',
        ];
    }
}
