<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGymOwnersColumnsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('manager_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('logo')->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->tinyInteger('terms_conditions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('manager_name');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('logo');
            $table->dropColumn('terms_conditions');
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
    }
}
