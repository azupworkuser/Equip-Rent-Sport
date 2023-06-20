<?php

namespace App\CoreLogic\Services;

use App\Events\UserInvitation\UserInvitationAccepted;
use App\Events\UserInvitation\UserInvitationCreated;
use App\Events\UserInvitation\UserInvitationDeleted;
use App\Events\UserInvitation\UserInvitationUpdated;
use App\Models\States\UserInvitation\Accept;
use App\Models\User;
use App\Models\UserInvitation;
use App\CoreLogic\Repositories\UserInvitationRepository;
use Illuminate\Support\Str;

class UserInvitationService extends Service
{
    protected string $repositoryName = UserInvitationRepository::class;

    /**
     * @param  array  $userInvitation
     * @return bool|UserInvitation
     */
    public function create(array $userInvitation): bool|UserInvitation
    {
        $userInvitation = $this->prepareData($userInvitation);
        $userInvitation['token'] = $this->generateToken($userInvitation);
        $userInvitation['user_id'] = $this->getUser($userInvitation);
        $domains = $userInvitation['domains'];
        unset($userInvitation['domains']);
        $userInvitationModel = $this->repository->create($userInvitation);
        $userInvitationModel->domains()->sync($domains);
        UserInvitationCreated::dispatch($userInvitationModel->fresh());
        return $userInvitationModel;
    }

    /**
     * @param  array  $payload
     * @param $userInvitation
     * @return UserInvitation
     */
    public function update(array $payload, $userInvitation): UserInvitation
    {
        $this->prepareData($payload);
        $payload['user_id'] = $this->getUser($userInvitation);
        $domains = $payload['domains'];
        unset($payload['domains']);
        $is_new_status = false;
        if (collect($payload)->get('status') !== null) {
            if ($payload['status'] !== $userInvitation->status && $userInvitation->status->canTransitionTo($payload['status'])) {
                $userInvitation->status->transitionTo($payload['status']);
                $is_new_status = true;
            } else {
                unset($payload['status']);
            }
        }
        $userInvitation->update($payload);
        $userInvitation->domains()->sync($domains);
        UserInvitationUpdated::dispatch($userInvitation->fresh());
        if ($is_new_status && $userInvitation->user_id && $userInvitation->status->equals(Accept::class)) {
            UserInvitationAccepted::dispatch($userInvitation->fresh());
        }
        return $userInvitation;
    }

    /**
     * @param  UserInvitation  $userInvitation
     * @return void
     */
    public function archive(UserInvitation $userInvitation): void
    {
        $userInvitation->delete();
        UserInvitationDeleted::dispatch($userInvitation->fresh());
    }

    /**
     * @param $attributes
     * @return false|string
     */
    public function generateToken($attributes)
    {
        return hash_hmac(
            'ripemd160',
            Str::random(16),
            $attributes['email'] . $attributes['domain_id'] . $attributes['tenant_id']
        );
    }

    /**
     * @param $attributes
     * @return null
     */
    public function getUser($attributes)
    {
        $user = User::where(['email' => $attributes['email']])->first();
        if ($user) {
            return $user->getKey();
        }
        return null;
    }

    /**
     * @param $inputData
     * @return mixed
     */
    private function prepareData(&$inputData)
    {
        $data = collect($inputData)->only(UserInvitation::getCustomColumns())->toArray();
        if (collect($data)->count() > 0) {
            $inputData['data'] = $data;
        }
        return $inputData;
    }

    /**
     * @param UserInvitation $userInvitation
     * @return UserInvitation
     */
    public function get(UserInvitation $userInvitation): UserInvitation
    {
        return $userInvitation->load('tenant');
    }
}
