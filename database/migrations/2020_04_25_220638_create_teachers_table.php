<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->boolean("is_global")->default(0);
            $table->timestamps();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade')
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
        Schema::dropIfExists('teachers');
    }
}
