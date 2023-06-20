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
        Schema::create('product_pricings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('unit_id')->nullable()->constrained('units');
            $table->foreignUuid('product_id')->constrained();
            $table->foreignUuid('tenant_id')->constrained();
            $table->foreignUuid('domain_id')->constrained();

            $table->string('pricing_structure_type')->description('eg. Fixed, By Person, Per Item, etc');
            $table->integer('min_quantity');
            $table->integer('max_quantity');
            $table->integer('price');
            $table->string('price_type')->description('eg. total or per Type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_pricings');
    }
};
