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
        Schema::create('product_availability_unit', function (Blueprint $table) {
            $table->foreignUuid('product_availability_id')->constrained();
            $table->foreignUuid('unit_id')->constrained();
            $table->foreignUuid('accompanying_unit_id')->nullable()->constrained('units');
            $table->integer('per_unit_price')->nullable();
            $table->integer('max_quantity_per_session')->nullable();
            $table->json('data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_availability_unit');
    }
};
