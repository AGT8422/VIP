<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Support\Facades\DB;

class AddSubParentIdToAccountTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_types', function (Blueprint $table) {
            $table->integer('sub_parent_id')->nullable();
        });

        $time = \Carbon::now();
        #......................create tree of accounts
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('1','1'  ,'Assets',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('2','11' ,'Fixed Asset',?,?,?,'1',?,?)",[1,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('3','111','Vehicles',?,?,?,'1',?,?)",[2,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('4','112','Furniture And Fixtures',?,?,?,'1',?,?)",[2,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('5','113','Office Equipment',?,?,?,'1',?,?)",[2,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('6','114','Tools',?,?,?,'1',?,?)",[2,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('7','12' ,'Current Asset',?,?,?,'1',?,?)",[1,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('8','121','Customers',?,?,?,'1',?,?)",[7,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('9','122','Other debitors',?,?,?,'1',?,?)",[7,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('10','123','Partners drawings',?,?,?,'1',?,?)",[7,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('11','124','Stock',?,?,?,'1',?,?)",[7,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('12','125','PREPAID EXPENSES',?,?,?,'1',?,?)",[7,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('13','126','Sec.Deposit',?,?,?,'1',?,?)",[7,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('14','13' ,'Cash holding',?,?,?,'1',?,?)",[1,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('15','131','Cash In Hand',?,?,?,'1',?,?)",[14,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('16','132','Bank',?,?,?,'1',?,?)",[14,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('17','133','card',?,?,?,'1',?,?)",[14,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('18','2'  ,'Liabilities',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('19','21' ,'Fixed liabilities',?,?,?,'1',?,?)",[18,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('20','211','Capital',?,?,?,'1',?,?)",[19,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('21','212','Current Account ',?,?,?,'1',?,?)",[19,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('22','22' ,'Current liabilities',?,?,?,'1',?,?)",[18,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('23','221','Suppliers',?,?,?,'1',?,?)",[22,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('24','222','Other creditors',?,?,?,'1',?,?)",[22,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('25','223','Other creditors',?,?,?,'1',?,?)",[22,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('26','224','Employees\\' receivables',?,?,?,'1',?,?)",[22,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('27','23' ,'Tax Vat',?,?,?,'1',?,?)",[18,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('28','3'  ,'Net purchases',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('29','31' ,'Purchases Account',?,?,?,'1',?,?)",[28,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('30','4'  ,'Net sales',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('31','41' ,'Sales Account',?,?,?,'1',?,?)",[30,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('32','5'  ,'Expenses',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('33','51' ,'The Expenses',?,?,?,'1',?,?)",[32,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('34','6'  ,'Revenues',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('35','7'  ,'Goods',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('36','8'  ,'Balance Sheet',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('37','9'  ,'both',?,?,?,'1',?,?)",[null,null,null,$time,$time]);
        DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('38','81' ,'Profit and loss',?,?,?,'1',?,?)",[36,null,null,$time,$time]);
        // DB::statement("INSERT INTO  account_types (id, code, name, parent_account_type_id, sub_parent_id, active, business_id, created_at, updated_at) VALUES ('36','71' ,'Goods Sub',?,?,?,'1',?,?)",[35,null,null,$time,$time]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_types', function (Blueprint $table) {
            //
        });
    }
}
