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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->string('code')->nullable();
            $table->foreignUuid('product_type_id')->constrained('product_types');
            $table->longText('description');
            $table->string('visibility')->comment('Everyone, Staff Only, View as Extras');
            $table->integer('advertised_price');
            $table->longText('terms_and_conditions')->nullable();
            $table->string('status');

            $table->foreignUuid('tenant_id')->constrained('tenants');
            $table->foreignUuid('created_by')->constrained('users');
            $table->foreignUuid('domain_id')->constrained('domains');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
