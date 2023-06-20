<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\CoreLogic\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        public ProductService $productService
    ) {
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product)
    {
        return response()->json(
            ProductResource::make($this->productService->get($product)),
            Response::HTTP_OK
        );
    }

    /**
     * @param StoreProductRequest $request
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request)
    {
        return response()->json([
            ProductResource::make(
                $this->productService->create($request->validated())
            ),
        ], Response::HTTP_CREATED);
    }
}
