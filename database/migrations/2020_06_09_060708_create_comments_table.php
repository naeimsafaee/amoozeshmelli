<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration{

    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::create('comments', function(Blueprint $table){
            $table->engine = "InnoDB";
            $table->id();
            $table->text("text");
            $table->unsignedBigInteger("section_id");
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("reply_to")->nullable();
            $table->timestamps();
        });

        Schema::table('comments', function(Blueprint $table){
            $table->foreign('section_id')->references('id')
                ->on('sections')->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('reply_to')->references('id')
                ->on('comments')->onDelete('cascade')
                ->onUpdate('cascade');
        });


    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::dropIfExists('comments');
    }
}
