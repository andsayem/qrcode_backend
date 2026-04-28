<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGiftsTableAddGiftTypeAndModifyPolicyType extends Migration
{
    
    public function up()
    {
        Schema::table('gifts', function (Blueprint $table) {
            // Check if the policy_type column exists
            if (!Schema::hasColumn('gifts', 'policy_type')) {
                // Add the policy_type column if it doesn't exist
                $table->string('policy_type')->nullable()->after('point_slab');
            } else {
                // Modify the policy_type column if it exists
                $table->string('policy_type')->change();
            }

            // Add the new gift_type column
            if (!Schema::hasColumn('gifts', 'gift_type')) {
                $table->string('gift_type')->nullable()->after('policy_type');
            }
        });
    }

    
    public function down()
    {
        Schema::table('gifts', function (Blueprint $table) {
            // Drop the gift_type column if it exists
            if (Schema::hasColumn('gifts', 'gift_type')) {
                $table->dropColumn('gift_type');
            }

            // Revert the policy_type column if needed
            if (Schema::hasColumn('gifts', 'policy_type')) {
                $table->string('policy_type')->change();
            }
        });
    }
};
