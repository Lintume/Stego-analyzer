<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PivotUserHasUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_user', function(Blueprint $table)
        {
            $table->integer('user_id')->unsigned();
            $table->integer('friend_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('social_users')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('social_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_user');
    }
}
