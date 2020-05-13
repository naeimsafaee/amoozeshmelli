<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradeToLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_to_lessons', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->unsignedBigInteger("grade_id");
            $table->unsignedBigInteger("lesson_id");
            $table->timestamps();
        });

        Schema::table('grade_to_lessons', function (Blueprint $table) {
            $table->foreign('grade_id')->references('id')
                ->on('grades')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('lesson_id')->references('id')
                ->on('lessons')->onDelete('cascade')
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
        Schema::dropIfExists('grade_to_lessons');
    }
}
