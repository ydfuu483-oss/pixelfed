<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lees_video_likes', function (Blueprint $table) {
            $table->id();
            $table->uuid('video_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('video_id')->references('id')->on('lees_videos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['video_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lees_video_likes');
    }
};