<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    use FormRequestHelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->tokenCan('inventory.asset.update') && tenant()->getKey() === $this->route('asset')->tenant_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', Rule::unique('assets')->where('tenant_id', tenant()->getKey())->ignore($this->asset)],
            'quantity' => 'required|integer',
            'capacity_per_quantity' => 'required|integer',
            'shared_between_products' => 'required|boolean',
            'shared_between_bookings' => 'required|boolean',
            'maintenance_booking_gap_minutes' => 'nullable|integer',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'nullable|exists:categories,id,tenant_id,' . tenant()->getKey(),
        ];
    }
}
