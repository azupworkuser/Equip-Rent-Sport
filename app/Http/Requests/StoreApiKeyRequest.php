<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Ips\CommaSeparatedIps;

class StoreApiKeyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return tenant()->isInAdminTeam();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'permissions' => ['required', 'array', 'permissions.*' => 'exists:permissions,id'],
            'name' => ['required', 'min:3', 'max:255', 'string'],
            'whitelist_ips' => [ 'nullable', new CommaSeparatedIps()]
        ];
    }
}
