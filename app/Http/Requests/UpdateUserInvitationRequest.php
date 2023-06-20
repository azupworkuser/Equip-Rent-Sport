<?php

namespace App\Http\Requests;

use App\Models\States\UserInvitation\UserInvitationStates;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\ModelStates\Validation\ValidStateRule;

class UpdateUserInvitationRequest extends FormRequest
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
            'domains' => 'required|array',
            'status' => ['required', new ValidStateRule(UserInvitationStates::class)],
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
        ];
    }
}
