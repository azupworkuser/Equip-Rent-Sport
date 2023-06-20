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
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('quantity')->nullable();
            $table->integer('capacity_per_quantity')->nullable();
            $table->boolean('shared_between_products');
            $table->boolean('shared_between_bookings');
            $table->string('status');

            $table->foreignUuid('tenant_id')->constrained();
            $table->foreignUuid('domain_id')->constrained();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
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
        Schema::dropIfExists('assets');
    }
};
