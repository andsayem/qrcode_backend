<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftPoliciesTable extends Migration
{
    public function up()
    {
        Schema::create('gift_policies', function (Blueprint $table) {
            $table->id();

            $table->string('program_name');   // Program name
            $table->date('start_date');       // Start date
            $table->date('end_date');         // End date

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gift_policies');
    }
}