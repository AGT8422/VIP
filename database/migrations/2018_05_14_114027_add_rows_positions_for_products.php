<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRowsPositionsForProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         

        Schema::table('product_racks', function (Blueprint $table) {
            $table->string('row')->after('rack')->nullable();
            $table->string('position')->after('row')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         

        Schema::table('product_racks', function (Blueprint $table) {
            $table->dropColumn('row');
            $table->dropColumn('position');
        });
    }
}
