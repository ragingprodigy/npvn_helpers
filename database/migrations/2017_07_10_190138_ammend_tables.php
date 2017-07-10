<?php

use App\Models\CollectionCenter;
use App\Models\LocalGovernment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmmendTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(CollectionCenter::TABLE_NAME, function (Blueprint $table) {
            $table->string('state')->nullable();
        });

        Schema::table(LocalGovernment::TABLE_NAME, function (Blueprint $table) {
            $table->string('state')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(CollectionCenter::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table(LocalGovernment::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
}
