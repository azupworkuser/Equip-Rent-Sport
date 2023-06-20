<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CoreLogic\Services\CustomerService;
use App\Http\Requests\SaveCustomerRequest;
use App\CoreLogic\Services\UserService;
use Illuminate\Http\Response;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Filters\Customers\SearchCustomer;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Models\Sorts\Customers\NameSort;

class CustomerController extends Controller
{
    public function __construct(
        public CustomerService $customerService,
        public UserService $userService,
    ) {
    }

     /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return CustomerResource::collection(
            $this
                ->customerService
                ->all(
                    paginate: true,
                    allowedFilters: [
                        'id', 'first_name', 'last_name', 'email', 'phone', 'country',
                        AllowedFilter::callback('search_customer', new SearchCustomer())
                    ],
                    allowedSorts: ['first_name', 'last_name', 'email', 'phone', 'country'],
                )
        );
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveCustomerRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Successfully added a customer!!',
            'customer' => CustomerResource::make(
                $this->customerService->create($request->validated())
            ),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveCustomerRequest $request, Customer $customer): JsonResponse
    {
        return response()->json([
            'message' => 'Customer updated successfully',
            'customer' => CustomerResource::make($this->customerService->update($customer, $request->validated())),
        ], Response::HTTP_OK);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->delete($customer);
        return response()->json(
            [
                'message' => 'Deleted customer!!'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return response()->json(
            CustomerResource::make($this->customerService->find($id)),
            Response::HTTP_OK
        );
    }
}
