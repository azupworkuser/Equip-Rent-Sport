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
        Schema::create('product_availability_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->foreignUuid('product_id')->constrained('products');
            $table->foreignUuid('tenant_id')->constrained('tenants');
            $table->foreignUuid('domain_id')->constrained('domains');
            $table->json('assets')->nullable();
            $table->foreignUuid('product_availability_id')->constrained('product_availabilities');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_availability_slots');
    }
};
