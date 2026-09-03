<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeIdLatLongAddressToCodeVerifyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('code_verify_logs', function (Blueprint $table) {
            $table->integer('code_id')->nullable()->after('code');
            $table->string('lat', 45)->nullable()->after('status');
            $table->string('long', 45)->nullable()->after('lat');
            $table->string('address', 255)->nullable()->after('long');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('code_verify_logs', function (Blueprint $table) {
            $table->dropColumn(['code_id', 'lat', 'long', 'address']);
        });
    }
}
