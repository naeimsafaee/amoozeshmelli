<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserToQuizzesTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::create('user_to_quizzes', function(Blueprint $table){
            $table->engine = "InnoDB";
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("quiz_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::dropIfExists('user_to_quizzes');
    }
}
