<?php

namespace App\CoreLogic\Services;

use App\Models\Product;
use App\CoreLogic\Repositories\ProductLocationRepository;

class ProductLocationService extends Service
{
    public string $repositoryName = ProductLocationRepository::class;

    /**
     * @param Product $product
     * @param array $data
     * @return mixed
     */
    public function update(Product $product, array $data)
    {
        return $this->repository->updateOrCreate(
            [
                'product_id' => $product->getKey(),
                'address_type' => $data['address_type'],
            ],
            $data
        );
    }
}
