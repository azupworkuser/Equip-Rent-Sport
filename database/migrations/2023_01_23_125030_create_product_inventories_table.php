<?php

use App\Models\ProductInventory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('product_id')->constrained();
            $table->foreignUuid('tenant_id')->constrained();
            $table->foreignUuid('domain_id')->constrained();
            $table->foreignUuid('created_by')->constrained('users');
            $table->string('inventory_type');
            $table->integer('quantity')->nullable();
            $table->integer('capacity_per_quantity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_inventories');
    }
};
