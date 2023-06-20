<?php

namespace Tests\Feature\CoreLogic\Services\Inventory;

use App\CoreLogic\Services\Inventory\DynamicInventory;
use App\CoreLogic\Services\Inventory\FixedInventory;
use App\CoreLogic\Services\Inventory\Inventory;
use App\CoreLogic\Services\Inventory\UnlimitedInventory;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use DatabaseMigrations;

    public function test_dynamic_inventory_factory_resolves_correct_instance()
    {
        $dynamicInventory = Inventory::factory('dynamic');
        $this->assertInstanceOf(DynamicInventory::class, $dynamicInventory);
    }

    public function test_fixed_inventory_factory_resolves_correct_instance()
    {
        $dynamicInventory = Inventory::factory('fixed');
        $this->assertInstanceOf(FixedInventory::class, $dynamicInventory);
    }

    public function test_unlimited_inventory_factory_resolves_correct_instance()
    {
        $dynamicInventory = Inventory::factory('unlimited');
        $this->assertInstanceOf(UnlimitedInventory::class, $dynamicInventory);
    }
}
