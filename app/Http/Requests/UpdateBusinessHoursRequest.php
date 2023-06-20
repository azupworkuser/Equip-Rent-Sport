<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessHoursRequest extends FormRequest
{
    use FormRequestHelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return tenant()->getKey() === $this->route('domain')->tenant_id && tenant()->isInAdminTeam(auth()->id());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'business_hours' => 'required|array',
            'business_hours.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'business_hours.*.open_time' => 'required|date_format:H:i:s',
            'business_hours.*.close_time' => 'required|date_format:H:i:s',
            'week_starts_on' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ];
    }
}
