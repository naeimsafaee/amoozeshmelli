<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string("title")->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('quiz_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamps();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('image_id')->references('id')
                ->on('images')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('quiz_id')->references('id')
                ->on('quizzes')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('lesson_id')->references('id')
                ->on('lessons')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('subject_id')->references('id')
                ->on('subjects')->onDelete('cascade')
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
        Schema::dropIfExists('questions');
    }
}
