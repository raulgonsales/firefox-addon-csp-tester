<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddonIdToCspReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('csp_reports', function (Blueprint $table) {
            $table->bigInteger('addon_id')->unsigned()->after('id');
            $table->string('test_type')->after('addon_id');
        });

        Schema::table('csp_reports', function($table) {
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
        Schema::table('csp_reports', function (Blueprint $table) {
            $table->dropColumn('addon_id');
        });
    }
}
