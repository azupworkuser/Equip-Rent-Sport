<?php

namespace App\CoreLogic\Services;

use App\CoreLogic\Repositories\CustomerRepository;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\Customer\CustomerCreated;
use App\Events\Customer\CustomerUpdated;
use App\Events\Customer\CustomerDeleted;

class CustomerService extends Service
{
    protected string $repositoryName = CustomerRepository::class;

    /**
     * @param array $data
     * @return bool|Customer
     */
    public function create(array $data): bool | Customer
    {
        $customerModel =  $this->repository->create($data);
        CustomerCreated::dispatch($customerModel->fresh());
        return $customerModel;
    }

    /**
     * @param Customer $customer
     * @return bool
     */
    public function delete(Customer $customer): bool
    {
        $customer->delete();
        CustomerDeleted::dispatch($customer->fresh());
        return true;
    }

    /**
     * @param Customer $customer
     * @param array $data
     * @return bool|Customer
     */
    public function update(Customer $customer, array $data): bool | Customer
    {
         $this->repository->setModel($customer)->update($data);
         CustomerUpdated::dispatch($customer->fresh());
         return $customer->fresh();
    }
}
