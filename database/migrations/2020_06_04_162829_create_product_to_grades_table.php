<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductToGradesTable extends Migration{

    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::create('product_to_grades', function(Blueprint $table){
            $table->engine = "InnoDB";
            $table->id();
            $table->unsignedBigInteger("product_id");
            $table->unsignedBigInteger("grade_id");
            $table->timestamps();
        });

        Schema::table('product_to_grades', function(Blueprint $table){
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::dropIfExists('product_to_grades');
    }
}
