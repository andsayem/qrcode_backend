<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lotteries', function (Blueprint $table) {
            $table->id();
            $table->string('title');

            $table->date('from_date');
            $table->date('to_date');
        
            $table->integer('required_points');
        
            $table->enum('status', [
                'pending',
                'running',
                'completed'
            ])->default('pending');
        
            $table->integer('current_position')->nullable();
        
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('lotteries');
    }
}
