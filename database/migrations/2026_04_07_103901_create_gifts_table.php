<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftsTable extends Migration
{
    public function up()
    {
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();

            // 🔗 Foreign Key (gift_policies)
            $table->foreignId('policy_id')
                  ->constrained('gift_policies')
                  ->cascadeOnDelete();

            // 🎯 Gift Data
            $table->integer('point_slab');
            $table->string('gift_name');

            // 🏷️ Type (instant / year_end)
            $table->enum('gift_type', ['instant', 'year_end']);

            // 💰 Point cut or free
            $table->boolean('is_point_cut')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gifts');
    }
}