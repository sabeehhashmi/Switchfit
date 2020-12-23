<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pass_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('buyer_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('card_id')->nullable();
            $table->string('stripe_card_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_transaction_id')->nullable();
            $table->double('total')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('pass_payments');
    }
}
