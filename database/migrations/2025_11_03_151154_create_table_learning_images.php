<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLearningImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learning_id');
            $table->string('path')->nullable();
            $table->foreign('learning_id')->references('id')->on('learnings');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learning_images');
    }
}
