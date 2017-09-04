<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLgasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\App\Models\Lga::TABLE_NAME, function (Blueprint $table) {
            $table->string('geocoded_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\App\Models\Lga::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('geocoded_address');
        });
    }
}
