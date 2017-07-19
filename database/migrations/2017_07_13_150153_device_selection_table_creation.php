<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeviceSelectionTableCreation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\App\User::TABLE_NAME, function (Blueprint $table) {
            $sql = file_get_contents(base_path('database/seeds/device_selection_dump.sql'));
            DB::connection()->getPdo()->exec($sql);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\App\Models\DeviceSelection::TABLE_NAME);
        Schema::dropIfExists(\App\Models\SelectableDevice::TABLE_NAME);
        Schema::dropIfExists(\App\Models\Lga::TABLE_NAME);
        Schema::dropIfExists(\App\Models\State::TABLE_NAME);
    }
}
