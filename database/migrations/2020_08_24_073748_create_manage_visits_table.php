<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManageVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manage_visits', function (Blueprint $table) {
            $table->id();
            $table->integer('order_item_id')->nullable();
            $table->string('pass_token')->nullable();
            $table->string('time')->nullable();
            $table->integer('current_visit_no')->nullable();
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
        Schema::dropIfExists('manage_visits');
    }
}
