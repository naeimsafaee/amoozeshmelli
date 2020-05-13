<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string("title");
            $table->unsignedBigInteger("lesson_id");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('subjects', function (Blueprint $table) {
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
        Schema::dropIfExists('subjects');
    }
}
