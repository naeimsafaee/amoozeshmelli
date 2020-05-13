<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string("title");
            $table->integer("price");
            $table->integer("gift_price");
            $table->unsignedBigInteger("grade_id");
            $table->boolean("download_able")->default(true);
            $table->string("file_path")->nullable();
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('grade_id')->references('id')
                ->on('grades')->onDelete('cascade')
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
        Schema::dropIfExists('products');
    }
}
