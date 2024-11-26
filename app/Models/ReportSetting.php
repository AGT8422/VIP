<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class ReportSetting extends Model
{
    use HasFactory;
      
    
    // ...1 Dashboard api react front end 
    public static function allData($date,$business_id) {
        // ************* Requirement ************* \\
        $reportSetting       =  \App\Models\ReportSetting::select("*")->first();
        $AccountSetting      =  \App\Models\SystemAccount::where("business_id",$business_id)->select("*")->first();
       
        if($date == "today"){
            //.......
            $Start         = \Carbon::now()->copy()->startOfDay();
            $StartBefore   = \Carbon::now()->copy()->startOfDay()->subDay();
            $End           = \Carbon::now();
        }elseif($date == "month"){
            //.......
            $Start         = \Carbon::now()->subMonth(0)->day(0);
            $StartBefore   = \Carbon::now()->subMonth(0)->day(0)->subMonth();
            $End           = \Carbon::now()->subMonth(0);
        }elseif($date == "year"){
            //.......
            $Start         = \Carbon::now()->subYear(1);
            $StartBefore   = \Carbon::now()->subYear(1)->subYear();
            $End           = \Carbon::now()->subYear(0);
        }elseif($date == "semi_annual"){
            //.......
            $Start         = \Carbon::now()->subYear(1)->addMonths(6);
            $StartBefore   = \Carbon::now()->subYear(1)->subYear()->addMonths(6);
            $End           = \Carbon::now()->subYear(0);
        }elseif($date == "quart"){
            //.......
            $Start         = \Carbon::now()->subYear(1)->addMonths(3);
            $StartBefore   = \Carbon::now()->subYear(1)->subYear()->addMonths(3);
            $End           = \Carbon::now()->subYear(0);
        }else{
            $Start         = \Carbon::now()->copy()->startOfDay();
            $End           = \Carbon::now();
        }
        
        $repoActual                   = ReportSetting::repo($AccountSetting,$Start,$End); 
        $repoBefore                   = ReportSetting::repo($AccountSetting,$StartBefore,$Start); 

        $Sale                         = $repoActual['Sale'];
        $Purchase                     = $repoActual['Purchase'];
        
        $SaleBefore                   = $repoBefore['Sale'];
        $PurchaseBefore               = $repoBefore['Purchase'];

        $salePercent                  = ($SaleBefore != 0 )? ($Sale-$SaleBefore)/$SaleBefore:100;
        $PurchasePercent              = ($PurchaseBefore != 0 )? ($Purchase-$PurchaseBefore)/$PurchaseBefore:100;
      
        // ....    
        $location_id                  = \App\BusinessLocation::allLocation($business_id);
        $transaction_types = [
            'purchase_return', 'sell_return', 'expense'
        ];
        // ** End Requirement ********************* \\
        // *********** Sale            ************ \\
        // *********** Purchase        ************ \\
        // *********** Debit Sale      ************ \\
        // *********** Debit Purchase  ************ \\
        // ***********      Expense    ************ \\

        $repoExpense          = ReportSetting::repoExpense($reportSetting,$Start,$End);
        $repoExpenseBefore    = ReportSetting::repoExpense($reportSetting,$StartBefore,$Start);
        
        $Total_expense        = $repoExpense ;
        $Total_expensePercent = ($repoExpenseBefore != 0 )? $repoExpense/$repoExpenseBefore:100; ;

        $report = [
                "Sale_section"             => ["Sale" => $Sale, "Percent" => $salePercent] ,
                // "DebitSale"        => $SaleDebit,
                "Purchase_section"         => ["Purchase" => $Purchase ,"Percent" => $PurchasePercent] ,
                // "DebitPurchase"    => $PurchaseDebit,
                "Expense_section"          => ["Expense" => $Total_expense , "Percent" => $Total_expensePercent] ,
            ];
        //  .......  
        // $closing        = \App\Product::closing_stock($business_id);
        // ***  Profit
        $Cross_profit   = $Sale         - $Purchase;
        $Net_profit     = $Cross_profit - $Total_expense   ;
        $array = [
                    "Profit"    => $Net_profit,
                 ];    
        $currency = \App\Models\ExchangeRate::where("business_id",$business_id)->where("source",1)->first();
        $symbol   =  \App\Currency::find($currency->currency_id);

        $response             =  [];
        $response['report']   =  $report; 
        $response['symbol']   =  ($symbol!=null)?$symbol->symbol:""; 
        $response['currency'] =  ($symbol!=null)?$symbol->currency:""; 
        $response['array']    =  $array; 

        return $response ;
             
    }
    // ...2 Get TOTAL by Type
    public static function getTotalByType($type,$reportSetting,$start_date,$end_date){
        $ids         = [];
        $accounts    = [];
        $total       = [];
     
        if($reportSetting){   
          
            $output   = $reportSetting->expense;
           
            
            $account  = \App\Account::whereHas("account_type",function($query) use($output){
                                            $query->where("parent_account_type_id",$output);
                                            // $query->whereOr("sub_parent_id",$output);
                                            // $query->whereOr("accounts.id",$output);
                                    })->get();
            
            foreach($account as $it){
                    $accounts[]     = $it->id  ;
                        // dd($start_dat    e . " _ " . $end_date);
                    if($start_date != null && $end_date != null){
                        $amount         = \App\AccountTransaction::where("account_id",$it->id)->select(
                            \DB::raw("SUM(IF(type = 'debit',amount,-1*amount)) as total_amounts")
                                            )->whereHas("account",function($query){
                                            $query->where("cost_center",0);
                                    })->where("for_repeat",null)
                                    ->whereDate('operation_date', '>=', $start_date)
                                    ->whereDate('operation_date', '<=', $end_date)
                                    ->first();
                       
                    }else{
                        $amount         = \App\AccountTransaction::where("account_id",$it->id)->select(
                                                                    \DB::raw("SUM(IF(type = 'debit',amount,-1*amount)) as total_amounts")
                                                            )->whereHas("account",function($query){
                                                                    $query->where("cost_center",0);
                                                            })->where("for_repeat",null)->first();
                                                            
                    }
                    
                    
                    
                    $total[$it->id] = $amount->total_amounts  ;
            }    

        }
        if($start_date != null && $end_date != null){
                $ac    = \App\AccountTransaction::whereIn("account_id",$accounts)->select(
                                        \DB::raw("SUM(IF(type = 'debit',amount,-1*amount)) as total_amounts")
                                    )->whereHas("account",function($query){
                                        $query->where("cost_center",0);
                                })->where("for_repeat",null)->whereDate('operation_date', '>=', $start_date)
                                ->whereDate('operation_date', '<=', $end_date)
                                ->first() ; 
        }else {
            $ac    = \App\AccountTransaction::whereIn("account_id",$accounts)->select(
                            \DB::raw("SUM(IF(type = 'debit',amount,-1*amount)) as total_amounts")
                        )->whereHas("account",function($query){
                            $query->where("cost_center",0);
                    })->where("for_repeat",null) 
                    ->first() ;
                
        }
     
     $output_array = [
                        "total"=>$ac,
                        "accounts"=>$total,
                    ];
        return $output_array;

    }
    // ...3 Currencies 
    public static function currencies($business_id,$date){
      
        $currency          = \App\Models\ExchangeRate::where("business_id",$business_id)->where("source","!=",1)->get();
        $main_currency     = \App\Models\ExchangeRate::where("business_id",$business_id)->where("source",1)->first();
        $count_of_currency = $currency->count();
        $list_currency     = [];
        foreach($currency as $i){
            $list_currency[] =  [ 
                                       "id" => $i->currency->id, 
                                       "name" => $i->currency->currency,
                                       "symbol" => $i->currency->symbol 
            ];
        }
        $list_main_currency[] =  [ 
                                   "id" => $main_currency->currency->id, 
                                   "name" => $main_currency->currency->currency,
                                   "symbol" => $main_currency->currency->symbol 
        ];
        $array = [
                "amin_currency" => $list_main_currency,
                "count"         => $count_of_currency,
                "list"          => $list_currency
            ];
        return $array;  
    }
    // ...4 Users
    public static function Users($business_id,$date) {
        $users          = \App\User::where("business_id",$business_id)->get();
        $count_of_users = 0;
        $list_users     = [];
        foreach($users as $i){
            if($i->is_cmmsn_agnt == 0)
            {
                $list_users[] =  [ 
                    "id" => $i->id,
                    "name" => $i->first_name,
                    
                ];
                $count_of_users++;
            }
        }  
        $array = [
                "count" => $count_of_users,
                "list"  => $list_users
            ];
        return $array;  
    }
    // ...5 Customers
    public static function Customers($business_id,$date) {
        $customers      = \App\Contact::where("business_id",$business_id)->where("type","customer")->get();
        $count_of_customers = 0;
        $list_customers     = [];
        foreach($customers as $i){
                $account        = \App\Account::where("contact_id",$i->id)
                                                ->join("account_transactions as at","at.account_id","accounts.id")
                                                ->where("for_repeat")
                                                ->get();
                $list_customers[] =  [ 
                    "id" => $i->id,
                    "name" => ($i->name != null || $i->name != "")?$i->name:$i->first_name,
                    
                ];
                $count_of_customers++;
            
        }  
        $array = [
                "count" => $count_of_customers,
                "list"  => $list_customers
            ];
        return $array;  
    }
    // ...6 Suppliers
    public static function Suppliers($business_id,$date) {
        $suppliers      = \App\Contact::where("business_id",$business_id)->where("type","supplier")->get();
        $count_of_suppliers = 0;
        $list_suppliers     = [];
        foreach($suppliers as $i){
                $list_suppliers[] =  [ 
                    "id" => $i->id,
                    "name" => ($i->name != null || $i->name != "")?$i->name:$i->first_name,
                    
                ];
                $count_of_suppliers++;
            
        }  
        $array = [
                "count" => $count_of_suppliers,
                "list"  => $list_suppliers
            ];
        return $array;  
    }
    // ...7 User data
    public static function UserData($user){
        try{
            $user_id = $user->id;
            $transaction_purchase = ReportSetting::transactionData($user_id,"purchase");
            $transaction_sale     = ReportSetting::transactionData($user_id,"sale");
            $count_purchase       = count($transaction_purchase);
            $count_sale           = count($transaction_sale);
            return $output = [
                "sale"           => $transaction_sale,
                "purchase"       => $transaction_purchase,
                "count_purchase" => $count_purchase,
                "count_sale"     => $count_sale,
                "messages"       => "User Data Access Successfully",
            ];
        }catch(Exception $e){
            return $output = [
                "status"=>403,
                "messages"=>"Invalid Data"
            ];
        }
    }
    // ...8 Accounts Balance  
    public static function Accounts($user){
        try{
            $business             = \App\Business::find($user->business_id); 
            $account_cash         = ReportSetting::accountBalance($business->cash);
            $account_bank         = ReportSetting::accountBalance($business->bank);
            $cash_count           = count($account_cash);
            $bank_count           = count($account_bank);
            return $output = [
                "cash"           => $account_cash,
                "bank"           => $account_bank,
                "count_cash"     => $cash_count,
                "count_bank"     => $bank_count,
                "messages"       => "Accounts Data Access Successfully",
            ];
        }catch(Exception $e){
            return $output = [
                "status"=>403,
                "messages"=>"Invalid Data"
            ];
        }
    }
    // ...9 Vouchers Data
    public static function Vouchers($user,$type){
        try{
            $voucher              = ReportSetting::voucherType($type);
            $voucher_count        = count($voucher);
            return $output = [
                "voucher"        => $voucher,
                "voucher_count"  => $voucher_count,
                "messages"       => "Voucher Data Access Successfully",
            ];
        }catch(Exception $e){
            return $output = [
                "status"=>403,
                "messages"=>"Invalid Data"
            ];
        }   
    }
    // ..10 Payments
    public static function Payments($user){
        try{
            $payments              = ReportSetting::alLPayments($user);
            $payments_count        = count($payments);
            return $output = [
                "payment"        => $payments,
                "payment_count"  => $payments_count,
                "messages"       => "Payments Data Access Successfully",
            ];
        }catch(Exception $e){
            return $output = [
                "status"=>403,
                "messages"=>"Invalid Data"
            ];
        }   
    }
    
    // *** repo 1
    public static function final_data($Account_id,$Start,$End,$status,$type){
            $data          = \App\AccountTransaction::join("transactions as tr",
                                                        "tr.id",
                                                        "account_transactions.transaction_id"
                                                        )
                                                ->where("account_transactions.account_id",$Account_id)
                                                ->whereBetween('account_transactions.created_at', [$Start, $End])
                                                ->where("account_transactions.type",$status)
                                                ->where("tr.type",$type)
                                                ->select([\DB::raw("( SUM( account_transactions.amount )   )  as final")])
                                                ->with([
                                                    "transaction"
                                                    ])
                                                ->first();

            return $data;
    }
    // *** repo 2
    public static function final_data_credit($Account_id,$Start,$End,$status,$type){
            $data    = \App\AccountTransaction::join("transactions as tr",
                                                        "tr.id",
                                                        "account_transactions.transaction_id"
                                                        )
                                                ->leftJoin("transaction_payments as tp",
                                                        "tp.transaction_id",
                                                        "tr.id"
                                                        )
                                                ->where("account_transactions.account_id",$Account_id)
                                                ->whereBetween('account_transactions.created_at', [$Start, $End])
                                                ->where("account_transactions.type",$status)
                                                ->whereNotNull("tp.amount")
                                                ->where("tr.type",$type)
                                                ->select([
                                                        "tr.id as tran_id",
                                                        "account_transactions.id as act_id",
                                                        "tp.id as tp_id",
                                                        "tp.amount as tp_amount",
                                                        "account_transactions.amount as act_amount",
                                                        "tr.final_total as final_total",
                                                    ])
                                                ->select([\DB::raw("( SUM(IF(tp.amount <= account_transactions.amount    , tp.amount , (tr.final_total - tr.tax_amount)  )   ))  as final")])
                                                ->with([
                                                    "transaction",
                                                    "transaction.payment_lines"
                                                    ])
                                                ->first();

            return $data;
    } 
    // *** repo 3 
    public static function repo($AccountSetting,$Start,$End){
        $purchaseTransaction          = ReportSetting::final_data($AccountSetting->purchase,$Start,$End,"debit","purchase");
        $purchaseTransactionDis       = ReportSetting::final_data($AccountSetting->purchase_discount,$Start,$End,"debit","purchase");
        $purchaseTransactionReturn    = ReportSetting::final_data($AccountSetting->purchase_return,$Start,$End,"credit","purchase_return");
        $purchaseTransactionReturnDis = ReportSetting::final_data($AccountSetting->purchase_discount,$Start,$End,"credit","purchase_return");
        $purchaseTransactionCredit    = ReportSetting::final_data_credit($AccountSetting->purchase,$Start,$End,"debit","purchase");

        $saleTransaction              = ReportSetting::final_data($AccountSetting->sale,$Start,$End,"credit","sale");
        $saleTransactionDis           = ReportSetting::final_data($AccountSetting->sale_discount,$Start,$End,"credit","sale");
        $saleTransactionReturn        = ReportSetting::final_data($AccountSetting->sale_return,$Start,$End,"debit","sell_return");
        $saleTransactionReturnDis     = ReportSetting::final_data($AccountSetting->sale_discount,$Start,$End,"debit","sell_return");
        $saleTransactionCredit        = ReportSetting::final_data_credit($AccountSetting->sale,$Start,$End,"credit","sale");

        $Sale          =  $saleTransaction->final     -  $saleTransactionDis->final      -  $saleTransactionReturn->final     - $saleTransactionReturnDis->final;
        $Purchase      =  $purchaseTransaction->final -  $purchaseTransactionDis->final  -  $purchaseTransactionReturn->final - $purchaseTransactionReturnDis->final;
        
        $PurchaseDebit =  $Purchase - $purchaseTransactionCredit->final ;
        $SaleDebit     =  $Sale     - $saleTransactionCredit->final ;

        $allData['Sale']           = $Sale  ;
        $allData['Purchase']       = $Purchase ;
        $allData['PurchaseDebit']  = $PurchaseDebit;
        $allData['SaleDebit']      = $SaleDebit ;

        return $allData;
    }
    // *** repo 4
    public static function repoExpense($reportSetting,$Start,$End){
        $Expenses      = \App\Models\ReportSetting::getTotalByType("expense",$reportSetting,$Start,$End);
        $Total_expense = ($Expenses["total"]["total_amounts"] != null )?$Expenses["total"]["total_amounts"]:0;
        return $Total_expense ;
    }
    // *** repo 5
    public static function transactionData($id,$type){
        $transaction = \App\Transaction::where("created_by",$id)
                                        ->where("type",$type)
                                        ->get();
        $dataAll = [];
        foreach($transaction as $i){
            if($i->type == "purchase"){
                $invoice_no = $i->ref_no;
            }else{
                $invoice_no = $i->invoice_no;
            }
            $data  = [
                "id"               => $i->id,
                "type"             => $i->type,
                "payment_status"   => $i->payment_status,
                "status"           => $i->status,
                "contact"          => $i->contact->name,
                "invoice_no"       => $invoice_no,
                "transaction_date" => $i->transaction_date,
                "final_total"      => $i->final_total,
            ];
            $dataAll[] = $data; 
        }
        return $dataAll;
    }
    // *** repo 6
    public static function accountBalance($account_id){
        $accounts = \App\Account::join("account_transactions as at","at.account_id","accounts.id")
                                        ->where("account_type_id",$account_id)
                                        ->whereNull("for_repeat")
                                        ->select([
                                        "accounts.id   as  account_id",  
                                        "accounts.name as account_name",  
                                        "accounts.account_type_id",  
                                        "accounts.account_number  as account_number_code",  
                                        "at.amount as amount",  
                                        "at.type",
                                        \DB::raw("SUM(IF(at.type = 'credit',at.amount*-1,at.amount)) as balance")  
                                        ])
                                        ->groupby("accounts.id")
                                        ->get();
           
        $dataAll  = [];
        foreach($accounts  as $it){
            if($it->account_id != null)
            {
                $data = [
                    "id"              => $it->account_id,
                    "title"           => $it->account_type->name,
                    "subtitle"        => $it->account_name,
                    "amount"          => ($it->balance<0)?($it->balance)*-1:$it->balance,
                    "account_number"  => $it->account_number_code,
                    "type"            => ($it->balance!=0)?(($it->balance<0)?"Credit":"Debit"):"",
                    "imgSrc"          => "",
                ];
            $dataAll[] = $data;
            }
        }
        return $dataAll;
    }
    // *** repo 7
    public static function voucherType($type){
        $dataAll  = [];
        if($type == "expense"){
            $voucher = \App\Models\GournalVoucher::get();
            foreach($voucher  as $it){
                $items = \App\Models\GournalVoucherItem::where("gournal_voucher_id",$it->id)->select(\DB::raw("SUM(amount) as amounts"))->first();
                $data  = [
                    "id"               => $it->id,
                    "voucher_number"   => $it->ref_no,
                    "main_account"     => $it->account->name,
                    "amount"           => $items->amounts,
                    "date"             => $it->date,
                ];  
                $dataAll[] = $data;
            }
        }elseif($type == "daily_payment"){
            $voucher = \App\Models\DailyPayment::get();
            $total   = 0;
            foreach($voucher  as $it){
                 $data = [
                    "id"              => $it->id,
                    "voucher_number"  => $it->ref_no,
                    "amount"          => $it->amount,
                    "date"            => $it->date,
                ];
                $dataAll[] = $data;
                $total     += $it->amount;
            }
        }else{
            $voucher = \App\Models\PaymentVoucher::get();
            
            foreach($voucher  as $it){
               $data = [
                    "id"              => $it->id,
                    "voucher_number"  => $it->ref_no,
                    "type"            => ($it->type == 1)?"Receipt Voucher":"Payment Voucher",
                    "amount"          => $it->amount,
                    "contact"         => $it->contact->name,
                    "account"         => $it->account->name,
                    "text"            => $it->text,
                    "date"            => $it->date,
                ];
                $dataAll[] = $data;
            }
        }
        return $dataAll;
    }
    // *** repo 8
    public static function alLPayments($user){
        $payments = \App\TransactionPayment::where("created_by",$user->id)->get();
        $dataAll  = [];
        foreach($payments  as $it){
            $data = [
                "id"              => $it->id,
                "payment_number"  => $it->payment_ref_no,
                "invoice"         => ($it->transaction->invoice != null || $it->transaction->invoice != "")?$it->transaction->invoice:$it->transaction->ref_no,
                "invoice_type"    => ($it->transaction->invoice != null || $it->transaction->invoice != "")?"sale":"purchase",
                "amount"          => $it->amount,
                "method"          => $it->method,
                "date"            => $it->paid_on,
            ];
            $dataAll[] = $data;
        }
        return $dataAll;
    }

}
