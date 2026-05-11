<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotteryWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('gift_assign_id')
                ->constrained('lottery_gift_assign')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('position');

            $table->string('winner_name');
            $table->string('mobile_no');

            $table->timestamp('draw_time');

            $table->timestamps();

            $table->unique([
                'lottery_id',
                'user_id'
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
        Schema::dropIfExists('lottery_winners');
    }
}
