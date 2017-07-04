<?php

use App\Models\CollectionCenter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionCenterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(CollectionCenter::TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('address');
            $table->string('geocoded_address')->nullable();
            $table->integer('lga_id')->nullable();

            $table->timestamps();

            $table->index('address');
            $table->index('geocoded_address');
            $table->index('lga_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(CollectionCenter::TABLE_NAME);
    }
}
