<?php

namespace App\Http\Requests;

use App\Models\States\UserInvitation\UserInvitationStates;
use App\Models\UserInvitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Spatie\ModelStates\Validation\ValidStateRule;

class StoreUserInvitationRequest extends FormRequest
{
    use FormRequestHelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->tokenCan('user.invite');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'domains' => "required|array",
            'email' => ['required', 'email'],
            'status' => ['required', new ValidStateRule(UserInvitationStates::class)],
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        if (!$validator->fails()) {
            $validator->after(function (Validator $validator) {
                $data = $validator->getData();
                $exists = UserInvitation::where([
                    'email' => $data['email'],
                    'tenant_id' => tenant()->getKey()
                ])->exists();

                if ($exists) {
                    $validator->errors()->add('email', 'You have already invited this user.');
                }
            });
        }
    }
}
