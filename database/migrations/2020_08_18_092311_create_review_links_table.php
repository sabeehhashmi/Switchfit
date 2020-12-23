<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_links', function (Blueprint $table) {
            $table->id();
            $table->integer('given_by')->nullable();
            $table->integer('given_to')->nullable();
            $table->integer('gym_id')->nullable();
            $table->double('stars')->nullable();
            $table->integer('review_id')->nullable();
            $table->integer('review_option_id')->nullable();
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
        Schema::dropIfExists('review_links');
    }
}
