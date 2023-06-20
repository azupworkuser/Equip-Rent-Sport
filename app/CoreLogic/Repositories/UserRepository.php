<?php

namespace App\CoreLogic\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public string $modelName = User::class;

    /**
     * @param array $userData
     * @return User
     */
    public function create(array $userData): User
    {
        return $this->model->create($userData);
    }

    /**
     * @param Collection $data
     * @return bool
     */
    public function update(Collection $data): bool
    {
        return $this->model->update($data->toArray());
    }

    /**
     * @return Collection
     */
    public function getAllTeams(): Collection
    {
        return $this->model->teams;
    }
}
