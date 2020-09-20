<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile_num')->unique();
            $table->integer('tot_coins')->unsigned()->nullable()->default(0);
            $table->integer('tot_follower')->unsigned()->nullable()->default(0);
            $table->string('ref_code', 100)->nullable()->unique();
            $table->string('ref_by', 100)->nullable();
            $table->integer('reffral')->unsigned()->nullable()->default(0);
            $table->string('api_token', 100);
            $table->string('password', 100);
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
        Schema::dropIfExists('users');
    }
}
