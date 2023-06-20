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
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        Schema::table(
            $tableNames['model_has_permissions'],
            function (Blueprint $table) use ($columnNames) {
                $table->uuid($columnNames['model_morph_key'])->change();
            }
        );

        Schema::table(
            $tableNames['model_has_roles'],
            function (Blueprint $table) use ($columnNames) {
                $table->uuid($columnNames['model_morph_key'])->change();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
