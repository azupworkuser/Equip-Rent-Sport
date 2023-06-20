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
        Schema::create('product_availabilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained();
            $table->foreignUuid('tenant_id')->constrained();
            $table->foreignUuid('domain_id')->constrained();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->integer('duration')->nullable()->comment('Number of hours, minutes, etc');
            $table->string('duration_type')->comment('Hours, Minutes, etc.');
            $table->integer('increment')->nullable()->comment('Number of hours, minutes, etc');
            $table->string('increment_type')->comment('Hours, Minutes, etc.');
            $table->timestamp('start_date')->comment("eg. 2021-01-01 10:00:00 UTC")->nullable();
            $table->timestamp('end_date')->comment("eg. 2021-01-02 10:00:00 UTC")->nullable();
            $table->timestamp('start_time')->comment("eg. 09:00")->nullable();
            $table->timestamp('end_time')->comment("eg. 17:00")->nullable();
            $table->boolean('all_day')->comment('Is this all day event/booking?')->default(false);
            $table->json('available_days')->nullable()->comment('eg. [1,2,3,4,5,6,7] for all days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_availabilities');
    }
};
