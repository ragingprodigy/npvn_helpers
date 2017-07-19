<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActualDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\App\Models\Device::TABLE_NAME, function (Blueprint $table) {
            $table->text('misdn', 11)->nullable();
            $table->timestampTz('date_enrolled')->nullable();
            $table->integer('enrolled_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\App\Models\Device::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('misdn');
            $table->dropColumn('date_enrolled');
            $table->dropColumn('enrolled_by');
        });
    }
}
