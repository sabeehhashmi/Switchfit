<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFewColumnsBirthDayEtcInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('postal_code')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone')->nullable();
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
            $table->dropColumn('postal_code');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('gender');
            $table->dropColumn('emergency_name');
            $table->dropColumn('emergency_phone');
        });
    }
}
