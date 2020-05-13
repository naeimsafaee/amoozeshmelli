<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerFileToQuizzesTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::table('quizzes', function(Blueprint $table){
            $table->string("answer_file")->nullable()->after("early_price");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::table('quizzes', function(Blueprint $table){
            //
        });
    }
}
