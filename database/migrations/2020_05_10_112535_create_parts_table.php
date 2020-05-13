<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartsTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::create('parts', function(Blueprint $table){
            $table->engine = "InnoDB";
            $table->id();
            $table->unsignedBigInteger("section_id");
            $table->unsignedBigInteger("video_id")->nullable();
            $table->unsignedBigInteger("question_id")->nullable();
            $table->integer("order");
            $table->timestamps();
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->foreign('video_id')->references('id')
                ->on('videos')->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('question_id')->references('id')
                ->on('questions')->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('section_id')->references('id')
                ->on('sections')->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::dropIfExists('parts');
    }
}
