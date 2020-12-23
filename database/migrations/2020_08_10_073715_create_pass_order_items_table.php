<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pass_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->integer('buyer_id')->nullable();
            $table->integer('gym_owner_id')->nullable();
            $table->integer('gym_id')->nullable();
            $table->integer('pass_id')->nullable();
            $table->string('pass_token')->nullable();
            $table->double('price')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('valid_days')->nullable();
            $table->double('sub_total')->nullable();
            $table->integer('allow_visits')->nullable();
            $table->integer('user_visits')->nullable();
            $table->string('book_date')->nullable();
            $table->string('last_valid_date')->nullable();
            $table->tinyInteger('is_expire')->default(0);
            $table->tinyInteger('is_used')->default(0);
            $table->double('switch_fit_fee')->nullable();
            $table->double('gym_owner_amount')->nullable();
            $table->string('gym_owner_amount_status')->nullable();
            $table->string('payment_status')->nullable();

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
        Schema::dropIfExists('pass_order_items');
    }
}
