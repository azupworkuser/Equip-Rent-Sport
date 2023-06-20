<?php

namespace App\Http\Requests;

trait FormRequestHelperTrait
{
    public function validated($keys = null, $default = null)
    {
        return parent::validated($keys, $default) + [
            'tenant_id' => tenant()->getKey(),
            'domain_id' => currentSubdomain(),
            'created_by' => auth()->id(),
        ];
    }
}
