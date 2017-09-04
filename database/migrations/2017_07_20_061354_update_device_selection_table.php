<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeviceSelectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\App\Models\DeviceSelection::TABLE_NAME, function (Blueprint $table) {
            $table->integer('collection_center_id')->nullable();
            $table->integer('dispatched_by')->nullable();
            $table->timestampTz('date_dispatched')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\App\Models\DeviceSelection::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(['collection_center_id', 'dispatched_by', 'date_dispatched']);
        });
    }
}
