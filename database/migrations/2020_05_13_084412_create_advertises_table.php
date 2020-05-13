<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertises', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string("title");
            $table->unsignedBigInteger("video_id");
            $table->integer("gift");
            $table->integer("count");
            $table->integer("price");
            $table->timestamps();
        });
        Schema::table('advertises', function (Blueprint $table) {
            $table->foreign('video_id')->references('id')
                ->on('videos')->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertises');
    }
}
