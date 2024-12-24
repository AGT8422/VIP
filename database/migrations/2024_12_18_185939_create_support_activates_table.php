<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSupportActivatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_activates', function (Blueprint $table) {
            $table->increments('id');
            $table->text("email")->nullable();
            $table->text("mobile")->nullable();
            $table->text("whatsapp")->nullable();
            $table->text("email_activation_code")->nullable();
            $table->text("email_activation_token")->nullable();
            $table->text("mobile_activation_code")->nullable();
            $table->text("mobile_activation_token")->nullable();
            $table->text("whatsapp_activation_code")->nullable();
            $table->text("whatsapp_activation_token")->nullable();
            $table->text("key")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        $time = \Carbon::now();
        // DB::statement("INSERT INTO currencies (column1, column2) VALUES (?, ?)", [$column1, $column2]);
    
        // DB::statement("UPDATE business SET supplier_type_id = 100000  WHERE  id = 1");
        // DB::statement("UPDATE business SET customer_type_id = 100000  WHERE  id = 1");
        // DB::statement("UPDATE business SET cash = 100000  WHERE  id = 1");
        // DB::statement("UPDATE business SET bank = 100000  WHERE  id = 1");
        // DB::statement("UPDATE business SET assets = 100000  WHERE  id = 1");
        // DB::statement("UPDATE business SET liability = 100000  WHERE  id = 1");
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_activates');
    }
}
