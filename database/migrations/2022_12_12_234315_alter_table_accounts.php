<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('code');
          
        });
        $time = \Carbon::now();
        #........................ create Accounts
        
        
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('1' ,?,'1','Main Box'                                ,'13101',?,'','1','0',?,?,?,'1','13101','0','0.00')"      ,[null,15 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('2' ,?,'1','P.D.C Received'                          ,'1122',?,'','1','0',?,?,?,'1','1122','0','0.00')"        ,[null,4  ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('3' ,?,'1','Gurantee Cheque Recivable'               ,'1121',?,'','1','0',?,?,?,'1','1121','0','0.00')"        ,[null,4  ,null,$time,$time]); 
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('4' ,?,'1','P.D.C Issued'                            ,'220001',?,'','1','0',?,?,?,'1','220001','0','0.00')"    ,[null,22 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('5' ,?,'1','Gurantee Cheque Payable'                 ,'220002',?,'','1','0',?,?,?,'1','220002','0','0.00')"    ,[null,22 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('6' ,?,'1','CashCustomer'                            ,'12100001',?,'','1','0',?,?,?,'1','CO00001','0','0.00')" ,[1,8  ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('7' ,?,'1','Purchases'                               ,'311',?,'','1','0',?,?,?,'1','311','0','0.00')"          ,[null,29 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('8' ,?,'1','Purchases return'                        ,'312',?,'','1','0',?,?,?,'1','312','0','0.00')"          ,[null,29 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('9' ,?,'1','Purchase Discount'                       ,'313',?,'','1','0',?,?,?,'1','313','0','0.00')"          ,[null,29 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('10',?,'1','Shipping & Logistic Expenses'            ,'314',?,'','1','0',?,?,?,'1','314','0','0.00')"          ,[null,29 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('11',?,'1','Sales'                                   ,'411',?,'','1','0',?,?,?,'1','411','0','0.00')"          ,[null,31 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('12',?,'1','Sales return'                            ,'412',?,'','1','0',?,?,?,'1','412','0','0.00')"          ,[null,31 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('13',?,'1','Sales Discount'                          ,'413',?,'','1','0',?,?,?,'1','413','0','0.00')"          ,[null,31 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('14',?,'1','Vat Due'                                 ,'2301',?,'','1','0',?,?,?,'1','2301','0','0.00')"        ,[null,27 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('15',?,'1','Vat Expense'                             ,'2302',?,'','1','0',?,?,?,'1','2302','0','0.00')"        ,[null,27 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('16',?,'1','Vat Purchase'                            ,'2303',?,'','1','0',?,?,?,'1','2303','0','0.00')"        ,[null,27 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('17',?,'1','Vat Sales'                               ,'2304',?,'','1','0',?,?,?,'1','2304','0','0.00')"        ,[null,27 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('18',?,'1','Cash Supplier'                           ,'21100001',?,'','1','0',?,?,?,'1','21100001','0','0.00')",[null,23 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('19',?,'1','Partner Mr Anas'                         ,'2113',?,'','1','0',?,?,?,'1','2113','0','0.00')"        ,[null,20 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('20',?,'1','Bad debt'                                ,'503',?,'','1','0',?,?,?,'1','503','0','0.00')"          ,[null,32 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('21',?,'1','Currency Differentials Expenses'         ,'204',?,'','1','0',?,?,?,'1','204','0','0.00')"          ,[null,18 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('22',?,'1','RENT EXPENSES'                           ,'502',?,'','1','0',?,?,?,'1','502','0','0.00')"          ,[null,32 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('23',?,'1','Salary expenses'                         ,'501',?,'','1','0',?,?,?,'1','501','0','0.00')"          ,[null,32 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('24',?,'1','Extra Revenues'                          ,'61',?,'','1','0',?,?,?,'1','61','0','0.00')"            ,[null,34 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('25',?,'1','First Term Goods'                        ,'71',?,'','1','0',?,?,?,'1','71','0','0.00')"            ,[null,35 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('26',?,'1','Last Term Goods'                         ,'72',?,'','1','0',?,?,?,'1','72','0','0.00')"            ,[null,35 ,null,$time,$time]);
        DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('27',?,'1','Trading Account'                         ,'811',?,'','1','0',?,?,?,'1','811','0','0.00')"          ,[null,38 ,null,$time,$time]);
        // DB::statement("INSERT INTO  accounts ( id ,  contact_id ,  business_id ,  name ,  account_number ,  account_type_id ,  note ,  created_by ,  is_closed ,  deleted_at ,  created_at ,  updated_at ,  is_second_curr ,  code ,  cost_center ,  balance ) VALUES ('27',?,'1','Profit and loss'                         ,'81','','','1','0',?,?,?,'1','81','0','0.00')",[null,null,$time,$time]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['code']);
        });
    }
}
