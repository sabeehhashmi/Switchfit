<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrainerProfileColumnsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('avatar')->nullable();
            $table->text('about')->nullable();
            $table->string('qualification_1')->nullable();
            $table->string('qualification_2')->nullable();
            $table->string('document_type')->nullable();
            $table->string('document')->nullable();
            $table->string('document_expire_date')->nullable();
            $table->text('availability')->nullable();
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
            $table->dropColumn('country');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('avatar');
            $table->dropColumn('about');
            $table->dropColumn('qualification_1');
            $table->dropColumn('qualification_2');
            $table->dropColumn('document_type');
            $table->dropColumn('document');
            $table->dropColumn('document_expire_date');
            $table->dropColumn('availability');
        });
    }
}
