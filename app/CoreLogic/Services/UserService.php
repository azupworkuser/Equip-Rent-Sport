<?php

namespace App\CoreLogic\Services;

use App\Events\User\UserCreated;
use App\Models\User;
use App\CoreLogic\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class UserService extends Service
{
    protected string $repositoryName = UserRepository::class;

    /**
     * @param array $user
     * @return bool|User
     */
    public function create(array $user): bool|User
    {
        $userModel = $this->repository->create($user);
        UserCreated::dispatch($userModel->fresh());
        $userModel->assignAccess();
        return $userModel;
    }

    /**
     * @param $data
     * @param User $user
     * @return User
     */
    public function update($data, User $user): User
    {
        $roles = $data['domainRoles'];
        unset($data['domainRoles']);
        $user->update($data);
        $user->assignAccess($roles);
        return $user;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function uploadProfileImage(Request $request)
    {
        $storagePath = 'profile/' . Str::uuid() . "." . $request->file('image')->extension();
        Storage::disk('s3')->put($storagePath, file_get_contents($request->file('image')), 'public');
        $fileImageUrl = Storage::disk('s3')->url($storagePath);
        return $fileImageUrl;
    }

    /**
     * @param bool $paginate
     * @param array $allowedFilters
     * @param array $allowedSorts
     * @param array $load
     * @return Collection|LengthAwarePaginator
     */
    public function all(
        bool $paginate = false,
        array $allowedFilters = [],
        array $allowedSorts = [],
        array $load = []
    ): Collection|LengthAwarePaginator {
        $query = QueryBuilder::for(User::class)
            ->with([
                'teams' => function ($q) {
                    $q->where('tenant_id', tenant()->getKey());
                }
            ]);

        if (count($allowedFilters) > 0) {
            $query = $query->allowedFilters($allowedFilters);
        }

        if (count($allowedSorts) > 0) {
            $query = $query->allowedSorts($allowedSorts);
        }

        if (count($load) > 0) {
            $query = $query->allowedIncludes($load);
        }

        return $paginate ? $query->paginate() : $query->get();
    }
}
