<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeviceSelectionSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\App\Models\DeviceSelection::TABLE_NAME, function (Blueprint $table) {
            $table->integer('actual_device_id')->nullable();
            $table->integer('allocated_by')->nullable();
            $table->timestampTz('date_allocated')->nullable();

            $sql = "UPDATE device_selection SET selection_date = REPLACE(selection_date, '/17 ', '/2017 '); UPDATE device_selection SET selection_date = STR_TO_DATE(selection_date, '%m/%d/%Y %H:%i');";
            DB::connection()->getPdo()->exec($sql);

            $table->dateTime('selection_date')->nullable()->change();
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
            $table->dropColumn([
                'actual_device_id',
                'allocated_by',
                'date_allocated'
            ]);

            $sql = "UPDATE device_selection SET selection_date = REPLACE(selection_date, '/2017 ', '/17 ')";
            DB::connection()->getPdo()->exec($sql);
        });
    }
}
