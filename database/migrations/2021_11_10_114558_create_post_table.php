<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('post', function (Blueprint $table) {
        $table->id();
        $table->string('image_name');
        $table->enum('visibility',['Private','Public','Hidden'])->default('Hidden');
        $table->string('share_with');
        $table->bigInteger('user_id')->unsigned();
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users');

    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post');
    }
}
