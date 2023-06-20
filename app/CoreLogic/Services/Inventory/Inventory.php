<?php

namespace App\CoreLogic\Services\Inventory;

class Inventory
{
    /**
     * @param string $type
     * @return BaseInventory
     */
    public static function factory(string $type): BaseInventory
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($type) . 'Inventory';

        try {
            return resolve($class);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
