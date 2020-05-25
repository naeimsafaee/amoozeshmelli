<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToLessonTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::table('lessons', function(Blueprint $table){
            $table->unsignedBigInteger("image_id")->after("title")->default(4588);
        });

        Schema::table('lessons', function (Blueprint $table) {

            $table->foreign('image_id')->references('id')
                ->on('images')->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::table('lessons', function(Blueprint $table){
            //
        });
    }
}
