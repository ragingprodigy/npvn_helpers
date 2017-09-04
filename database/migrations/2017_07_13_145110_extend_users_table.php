<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtendUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\App\User::TABLE_NAME, function (Blueprint $table) {
            $table->boolean('can_unbundle')->default(false);
            $table->boolean('can_enroll')->default(false);
            $table->boolean('can_allocate')->default(false);
            $table->boolean('can_repack')->default(false);
            $table->boolean('is_admin')->default(false);

            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\App\User::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(['can_unbundle', 'can_enroll', 'can_allocate', 'can_repack']);
            $table->dropColumn(['is_admin', 'is_active']);
        });
    }
}
