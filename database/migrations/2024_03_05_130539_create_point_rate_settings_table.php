<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointRateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_rate_settings', function (Blueprint $table) {
            $table->id(); 
            $table->integer('point_rate');
            $table->unsignedBigInteger('setting_id');
            $table->integer('country_id');

            $table->timestamps();
            
            // $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_rate_settings');
    }
}
