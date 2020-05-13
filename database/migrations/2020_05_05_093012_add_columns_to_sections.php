<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSections extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::table('sections', function(Blueprint $table){
            $table->integer('early_price')->after('gift_price');
            $table->boolean('can_pass')->default(0)->after('opening_date');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::table('sections', function(Blueprint $table){
            //
        });
    }
}
