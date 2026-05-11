<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotteryGiftAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_gift_assign', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('gift_id')
                ->constrained('lottery_gifts')
                ->cascadeOnDelete();

            $table->integer('position');

            $table->timestamps();

            $table->unique([
                'lottery_id',
                'position'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lottery_gift_assign');
    }
}
