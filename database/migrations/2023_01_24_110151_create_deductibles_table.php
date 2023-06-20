<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deductibles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained();
            $table->foreignUuid('domain_id')->constrained();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
            $table->string('name');
            $table->unsignedTinyInteger('category')->comment('Tax, Fee')->index();
            $table->unsignedTinyInteger('type')->comment('Percentage, Fixed per order item, Fixed per quantity, Fixed per duration')->index();
            $table->decimal('value');
            $table->boolean('is_price_inclusive')->default(false);
            $table->boolean('is_compounded')->default(false);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deductibles');
    }
};
