<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeBankTokenAccountColumnsInBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->text('stripe_account')->nullable();
            $table->text('stripe_bank_token')->nullable();
            $table->text('stripe_bank_account')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn('stripe_account');
            $table->dropColumn('stripe_bank_token');
            $table->dropColumn('stripe_bank_account');
        });
    }
}
