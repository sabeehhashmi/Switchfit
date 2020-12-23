<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainerActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainer_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('name')->nullable();
            $table->double('price')->nullable();
            $table->longText('about')->nullable();
            $table->integer('duration')->nullable();
            $table->string('type')->nullable();
            $table->string('address')->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('trainer_activities');
    }
}
