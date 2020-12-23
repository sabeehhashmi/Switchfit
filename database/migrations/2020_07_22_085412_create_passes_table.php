<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('gym_id')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->double('price')->nullable();
            $table->integer('use_limit')->nullable();
            $table->integer('valid_days')->nullable();
            $table->tinyInteger('active')->nullable();
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
        Schema::dropIfExists('passes');
    }
}
