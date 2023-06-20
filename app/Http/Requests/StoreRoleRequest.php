<?php

namespace App\Http\Requests;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRoleRequest extends FormRequest
{
    use FormRequestHelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->tokenCan(Permission::PERM_USER_ROLE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'permissions' => ['required', Rule::exists('permissions', 'name')]
        ];
    }

    public function withValidator($validator)
    {
        if (!$validator->fails()) {
            $validator->after(function (Validator $validator) {
                $data = $validator->getData();
                $exists = Role::where([
                    'name' => $data['name'],
                    'tenant_id' => tenant()->getKey()
                ])->exists();

                if ($exists) {
                    $validator->errors()->add('name', 'You have already added this role.');
                }
            });
        }
    }
}
