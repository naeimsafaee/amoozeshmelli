<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerFileToQuestionsTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::table('questions', function(Blueprint $table){
            $table->string("answer_file")->nullable()->after("subject_id");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::table('questions', function(Blueprint $table){
            //
        });
    }
}
