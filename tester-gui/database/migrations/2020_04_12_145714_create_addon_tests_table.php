<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_tests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('addon_id')->unsigned();
            $table->string('type_name');
            $table->boolean('failed_test')->nullable();
        });

        Schema::table('addon_tests', function (Blueprint $table) {
            $table->foreign('addon_id')
                ->references('id')
                ->on('addons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_types');
    }
}
