<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_site', function (Blueprint $table) {
            $table->bigInteger('addon_id')->unsigned();
            $table->bigInteger('site_id')->unsigned();
            $table->string('content_scripts_count')->nullable();
            $table->string('content_scripts_count_with_signs')->nullable();
            $table->string('scripts_info')->nullable();
        });

        Schema::table('addon_site', function($table) {
            $table->foreign('addon_id')
                ->references('id')
                ->on('addons');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addon_site');
    }
}
