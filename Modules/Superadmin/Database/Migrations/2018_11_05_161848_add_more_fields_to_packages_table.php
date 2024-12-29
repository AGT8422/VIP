<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('is_private')->default(0)->after('is_active');
            $table->boolean('is_one_time')->default(0)->after('is_private');
            $table->boolean('enable_custom_link')->default(0)->after('is_one_time');
            $table->string('custom_link')->nullable()->after('enable_custom_link');
            $table->string('custom_link_text')->nullable()->after('custom_link');
            $table->text('enabled_modules')->nullable()->after('custom_link_text');
        });
        $time   = \Carbon::now();
        $module = '{\"manufacturing_module\":\"1\",\"repair_module\":\"1\",\"Warehouse\":\"1\"}' ;
        $query  = "INSERT INTO packages (id,name,description,location_count,user_count,product_count,bookings,kitchen,order_screen,tables,invoice_count,`interval`,interval_count,trial_days,price,custom_permissions,created_by,sort_order,is_active,is_private,is_one_time,enable_custom_link,custom_link,custom_link_text,deleted_at,created_at,updated_at,enabled_modules) VALUES ('1','TRIAL VERSION','Trial','0','2','0','0','0','0','0','0','days','15','0','0.0000',?,'1','1','1','0','1','0','','',?,?,?,'')" ;
        DB::statement($query,[$module,null,$time,$time]);
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
