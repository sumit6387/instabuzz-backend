<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuycoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buycoins', function (Blueprint $table) {
            $table->id();
            $table->string('tranfer_id')->unique();
            $table->foreignId('user_id');
            $table->integer('coins');
            $table->string('price');
            $table->string('place');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buycoins');
    }
}
