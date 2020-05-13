

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string('phone')->unique();
            $table->string('fullName')->nullable();
            $table->string('userName')->unique()->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->longText("remember_token")->nullable();
            $table->string('code')->nullable();
            $table->string('password')->nullable();
            $table->boolean("is_admin")->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('grade_id')->references('id')
                ->on('grades')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('city_id')->references('id')
                ->on('cities')->onDelete('cascade')
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
        Schema::dropIfExists('users');
    }
}
