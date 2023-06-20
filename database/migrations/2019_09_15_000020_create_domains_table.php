<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('domain', 255)->unique();
            $table->string('tenant_id');
            $table->unsignedTinyInteger('is_primary')->default(false);
            $table->unsignedTinyInteger('is_fallback')->default(false);
            $table->string('location_name');
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('primary_industry')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('dial_code')->nullable();
            $table->longText('company_description')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone_iso3')->nullable();
            $table->string('currency_iso3')->nullable();
            $table->string('language_iso2')->default('en');
            $table->string('order_number_format')->default('numeric');
            $table->string('order_number_prefix')->nullable();
            $table->string('date_format')->default('D, d M, Y');
            $table->string('time_format')->default('12h');
            $table->json('data')->nullable();

            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
