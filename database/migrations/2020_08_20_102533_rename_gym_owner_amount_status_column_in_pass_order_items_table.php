<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameGymOwnerAmountStatusColumnInPassOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pass_order_items', function (Blueprint $table) {
            $table->renameColumn('gym_owner_amount_status','payout_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pass_order_items', function (Blueprint $table) {
            $table->renameColumn('payout_status','gym_owner_amount_status');
        });
    }
}
