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
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->string('internal_name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('id_required')->default(false);
            $table->integer('seats_used')->default(1);

            $table->foreignUuid('unit_type_id')->constrained();
            $table->foreignUuid('tenant_id')->constrained();
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
        Schema::dropIfExists('units');
    }
};
