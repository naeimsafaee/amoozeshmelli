<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherToSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_to_subjects', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->unsignedBigInteger("teacher_id");
            $table->unsignedBigInteger("subject_id");
            $table->timestamps();
        });

        Schema::table('teacher_to_subjects', function (Blueprint $table) {

            $table->foreign('teacher_id')->references('id')
                ->on('teachers')->onDelete('cascade')
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
        Schema::dropIfExists('teacher_to_subjects');
    }
}
