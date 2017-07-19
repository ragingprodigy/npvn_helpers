<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnbundlingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Models\Unbundling::TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('actual_device_id');
            $table->boolean('power')->default(false);
            $table->boolean('accessories')->default(false);
            $table->boolean('assessment')->default(false);

            $table->integer('certified_by')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\App\Models\Unbundling::TABLE_NAME);
    }
}
