<?php

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
        Schema::create('asset_product_inventory', function (Blueprint $table) {
            $table->foreignUuid('product_inventory_id')->constrained();
            $table->foreignUuid('asset_id')->constrained();
            $table->primary(['product_inventory_id', 'asset_id']);
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
        Schema::dropIfExists('asset_product_inventory');
    }
};
