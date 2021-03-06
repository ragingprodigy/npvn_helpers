<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActualDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Models\Device::TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');
            $table->integer('available_device_id');
            $table->string('imei');
            $table->string('serial');

            $table->boolean('unbundled')->default(false);
            $table->boolean('enrolled')->default(false);
            $table->boolean('allocated')->default(false);
            $table->boolean('dispatched')->default(false);

            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->softDeletesTz();
            $table->timestampsTz();

            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\App\Models\Device::TABLE_NAME);
    }
}
