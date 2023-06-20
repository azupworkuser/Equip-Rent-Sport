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
        Schema::create('product_availability_time_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('product_availability_id')->constrained();
            $table->timestamp('starts_at')->nullable()->comment("eg. 2021-01-01 10:00:00 UTC");
            $table->timestamp('ends_at')->nullable()->comment("eg. 2021-01-01 10:00:00 UTC");
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
        Schema::dropIfExists('product_availability_time_slots');
    }
};
