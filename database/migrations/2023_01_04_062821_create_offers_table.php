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
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained();
            $table->foreignUuid('created_by')->constrained('users');
            $table->foreignUuid('domain_id')->constrained();
            $table->string('title');
            $table->string('code');
            $table->string('code_type');
            $table->decimal('amount');
            $table->string('status');
            $table->string('offer_type')->description('Voucher, Promo Code, Gift Card');
            $table->string('apply_once_per_type')->nullable();
            $table->integer('max_redemption_time')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->json('settings')->nullable();
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
        Schema::dropIfExists('offers');
    }
};
