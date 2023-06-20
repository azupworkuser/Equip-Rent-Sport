<?php

namespace App\CoreLogic\Services;

use App\Http\Requests\StoreAssetRequest;
use App\Models\Asset;
use App\CoreLogic\Repositories\AssetRepository;
use Illuminate\Support\Facades\Event;

class AssetService extends Service
{
    protected string $repositoryName = AssetRepository::class;

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $categoryIds = $data['category_ids'];
        unset($data['category_ids']);

        $asset = $this->repository->create($data);

        $asset->categories()->sync($categoryIds);

        Event::dispatch('asset.created', $asset);

        return $asset;
    }

    /**
     * @param Asset $asset
     * @param array $data
     * @return Asset|null
     */
    public function update(Asset $asset, array $data)
    {
        if (isset($data['category_ids'])) {
            $asset->categories()->sync($data['category_ids']);
            unset($data['category_ids']);
        }

        $this->repository->setModel($asset)->update($data);

        Event::dispatch('asset.updated', $asset);

        return $asset->load('categories')->fresh();
    }

    /**
     * @param Asset $asset
     * @return Asset
     */
    public function delete(Asset $asset)
    {
        $this->repository->setModel($asset)->delete();

        Event::dispatch('asset.deleted', $asset);

        return $asset;
    }
}
