<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProvidersColumnsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('device_id')->nullable();
            $table->string('device_type')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->text('api_token')->nullable();
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
            $table->dropColumn('device_id');
            $table->dropColumn('device_type');
            $table->dropColumn('provider');
            $table->dropColumn('provider_id');
            $table->dropColumn('api_token');
        });
    }
}
