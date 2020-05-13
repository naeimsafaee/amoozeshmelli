<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleToPercentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people_to_percents', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->unsignedBigInteger("people_id");
            $table->unsignedBigInteger("product_id")->nullable();
            $table->unsignedBigInteger("section_id")->nullable();
            $table->unsignedBigInteger("quiz_id")->nullable();
            $table->unsignedBigInteger("advertise_id")->nullable();
            $table->integer("percent");
            $table->timestamps();
        });
        Schema::table('people_to_percents', function (Blueprint $table) {
            $table->foreign('people_id')->references('id')
                ->on('people')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('section_id')->references('id')
                ->on('sections')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('quiz_id')->references('id')
                ->on('quizzes')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('advertise_id')->references('id')
                ->on('advertises')->onDelete('cascade')
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
        Schema::dropIfExists('people_to_percents');
    }
}
