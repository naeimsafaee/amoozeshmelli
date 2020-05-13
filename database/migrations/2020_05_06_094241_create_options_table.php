<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::create('options', function(Blueprint $table){
            $table->engine = "InnoDB";
            $table->id();
            $table->string("title")->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('question_id');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::table('options', function (Blueprint $table) {
            $table->foreign('image_id')->references('id')
                ->on('images')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('question_id')->references('id')
                ->on('questions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::dropIfExists('options');
    }
}
