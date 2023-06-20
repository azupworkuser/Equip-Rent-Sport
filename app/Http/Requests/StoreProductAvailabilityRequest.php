<?php

namespace App\Http\Requests;

use App\Models\ProductAvailability;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductAvailabilityRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'start_date' => 'required|after_or_equal:today',
            'end_date' => 'required|after_or_equal:start_date',
            'start_time' => 'required|date_format:G:i',
            'end_time' => 'required|date_format:G:i|after:start_time',
            'increment' => 'required|numeric|min:1',
            'increment_type' => 'required|in:' . ProductAvailability::DURATION_UNIT_TYPE_HOURS . ',' . ProductAvailability::DURATION_UNIT_TYPE_MINUTES,
            'duration' => 'required|numeric|min:1',
            'duration_type' => 'required|in:' . ProductAvailability::DURATION_UNIT_TYPE_HOURS . ',' . ProductAvailability::DURATION_UNIT_TYPE_MINUTES,
            'all_day' => ['nullable', 'boolean', 'default:false'],
            'available_days' => 'required|array|min:1',
        ];
    }
}
