<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string("title");
            $table->integer("price")->default(0);
            $table->integer("gift_price")->default(0);
            $table->unsignedBigInteger("teacher_id");
            $table->unsignedBigInteger("subject_id");
            $table->unsignedBigInteger("quiz_id");
            $table->unsignedBigInteger("pre_section_id")->default(0)->nullable();
            $table->date("opening_date");
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::table('sections', function (Blueprint $table) {

            $table->foreign('teacher_id')->references('id')
                ->on('teachers')->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('subject_id')->references('id')
                ->on('subjects')->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('quiz_id')->references('id')
                ->on('quizzes')->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('pre_section_id')->references('id')
                ->on('sections')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
