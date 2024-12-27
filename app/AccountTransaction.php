<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTransaction extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'operation_date',
        'created_at',
        'updated_at'
    ];

    protected  $appends =  ['parent_ref'];

    /**
     * Gives account transaction type from payment transaction type
     * @param  string $payment_transaction_type
     * @return string
     */
    public static function getAccountTransactionType($tansaction_type)
    {
        $account_transaction_types = [
            'sale'             => 'credit',
            'purchase'         => 'debit',
            'expense'          => 'debit',
            'stock_adjustment' => 'debit',
            'purchase_return'  => 'credit',
            'sell_return'      => 'debit',
            'payroll'          => 'debit',
            'expense_refund'   => 'credit'
        ];

        return $account_transaction_types[$tansaction_type];
    }

    //  **1********* ACCOUNT TRANSACTION  **********1** \\
        /**
         * Creates new account transaction
         * @return obj
         */
        public static function createAccountTransaction($data)
        {
            $check_id                    = NULL;
            $entry_id                    = NULL;
            $id_delete                   = NULL;
            $for_repeat                  = NULL;
            $purchase_line_id            = NULL;
            $transaction_array           = NULL;
            $cost_center_id_row          = NULL;
            $payment_voucher_id          = NULL;
            $gournal_voucher_id          = NULL;
            $return_transaction_id       = NULL;
            $daily_payment_item_id       = NULL;
            $gournal_voucher_item_id     = NULL;
            $transaction_sell_line_id    = NULL;
            $additional_shipping_item_id = NULL;

            if (isset($data['check_id']))                    { $check_id                    = $data['check_id'];                    }
            if (isset($data['entry_id']))                    { $entry_id                    = $data['entry_id'];                    }
            if (isset($data['id_delete']))                   { $id_delete                   = $data['id_delete'];                   }
            if (isset($data['for_repeat']))                  { $for_repeat                  = $data['for_repeat'];                  }
            if (isset($data['cs_related_id']))               { $cost_center_id_row          = $data['cs_related_id'];               }
            if (isset($data['purchase_line_id']))            { $purchase_line_id            = $data['purchase_line_id'];            }
            if (isset($data['transaction_array']))           { $transaction_array           = $data['transaction_array'];           }
            if (isset($data['payment_voucher_id']))          { $payment_voucher_id          = $data['payment_voucher_id'];          }
            if (isset($data['gournal_voucher_id']))          { $gournal_voucher_id          = $data['gournal_voucher_id'];          }
            if (isset($data['return_transaction_id']))       { $return_transaction_id       = $data['return_transaction_id'];       }
            if (isset($data['daily_payment_item_id']))       { $daily_payment_item_id       = $data['daily_payment_item_id'];       }
            if (isset($data['gournal_voucher_item_id']))     { $gournal_voucher_item_id     = $data['gournal_voucher_item_id'];     }
            if (isset($data['transaction_sell_line_id']))    { $transaction_sell_line_id    = $data['transaction_sell_line_id'];    }
            if (isset($data['additional_shipping_item_id'])) { $additional_shipping_item_id = $data['additional_shipping_item_id']; }
        
            $transaction_data = [ 
                'amount'                      => round($data['amount'],2),
                'account_id'                  => $data['account_id'],
                'type'                        => $data['type'],
                'sub_type'                    => !empty($data['sub_type']) ? $data['sub_type'] : null,
                'operation_date'              => !empty($data['operation_date']) ? $data['operation_date'] : \Carbon::now(),
                'created_by'                  => $data['created_by'],
                'transaction_id'              => !empty($data['transaction_id']) ? $data['transaction_id'] : null,
                'transaction_payment_id'      => !empty($data['transaction_payment_id']) ? $data['transaction_payment_id'] : null,
                'note'                        => !empty($data['note']) ? $data['note'] : null,
                'transfer_transaction_id'     => !empty($data['transfer_transaction_id']) ? $data['transfer_transaction_id'] : null,
                'check_id'                    => $check_id,
                'for_repeat'                  => $for_repeat,
                'payment_voucher_id'          => $payment_voucher_id,
                'daily_payment_item_id'       => $daily_payment_item_id,
                'gournal_voucher_item_id'     => $gournal_voucher_item_id,
                'purchase_line_id'            => $purchase_line_id,
                'transaction_sell_line_id'    => $transaction_sell_line_id,
                'additional_shipping_item_id' => $additional_shipping_item_id,
                'return_transaction_id'       => $return_transaction_id,
                'gournal_voucher_id'          => $gournal_voucher_id,
                'id_delete'                   => $id_delete,
                'transaction_array'           => $transaction_array,
                'entry_id'                    => $entry_id,
                'cs_related_id'               => $cost_center_id_row

            ];

            $account_transaction = AccountTransaction::create($transaction_data);

            return $account_transaction;
        }
        /**
         * Updates transaction payment from transaction payment
         * @param  obj $transaction_payment
         * @param  array $inputs
         * @param  string $transaction_type
         * @return string
         */
        public static function updateAccountTransaction($transaction_payment, $transaction_type)
        {
            if (!empty($transaction_payment->account_id)) {
                $account_transaction = AccountTransaction::where('transaction_payment_id', $transaction_payment->id )->first();
                if (!empty($account_transaction)) {
                    $account_transaction->amount     = round($transaction_payment->amount,2);
                    $account_transaction->account_id = $transaction_payment->account_id;
                    $account_transaction->save();
                    return $account_transaction;
                } else {
                    $account_trans_data = [
                        'amount'                 => round($transaction_payment->amount,2),
                        'account_id'             => $transaction_payment->account_id,
                        'type'                   => self::getAccountTransactionType($transaction_type),
                        'operation_date'         => $transaction_payment->paid_on,
                        'created_by'             => $transaction_payment->created_by,
                        'transaction_id'         => $transaction_payment->transaction_id,
                        'transaction_payment_id' => $transaction_payment->id
                    ];

                    //If change return then set type as debit
                    if ($transaction_payment->transaction->type == 'sale' && $transaction_payment->is_return == 1) {
                        $account_trans_data['type'] = 'debit';
                    }

                    self::createAccountTransaction($account_trans_data);
                }
            }
        }
    //  ************************************************ \\

    //  **2********* PURCHASE TRANSACTION  **********2** \\
        // .F.....................................................
        public static function add_purchase($data,$total=null,$type=null)
        {
            
            $setting          =  \App\Models\SystemAccount::where('business_id',$data->business_id)->first();
        
        
            //purchase account 
            $amount           =  $data->final_total - $data->tax_amount;
            $discount_amount  =  $data->discount_amount;
            $tax = 0;
            // .......................... prepare discount * 1
            if ($data->discount_type == 'percentage') {
                $discount_amount = $data->total_before_tax*($data->discount_amount/100);
            }elseif ($data->discount_type == 'fixed_after_vat') {
                if($data->tax != null){
                    $discount_amount  = ($data->discount_amount/(1+ ( $data->tax->amount/100 ) )) ;
                }else{
                    $discount_amount  = ($data->discount_amount/(1+ ( 0/100 ) )) ;
                }
                if($data->exchange_price != 0){
                    $discount_amount  = $discount_amount * $data->exchange_price ;
                }
            }else{
                $discount_amount  = $data->discount_amount ;
                if($data->exchange_price != 0){
                    $discount_amount  = $discount_amount * $data->exchange_price ;
                }
            }
            //.......................................... end * 1
            // $discount_amount  = round($discount_amount,config('constants.currency_precision')); 
            // ***** here
            //....... ADD ROW COST ENTRY............ cost center in purchase add *2
            $cost_center  = AccountTransaction::where('transaction_id',$data->id)
                                                ->whereHas('account',function($query){
                                                    $query->where('cost_center',1);
                                                })->where("note","!=","Add Expense")->first();
            if ($data->cost_center_id) {
                if (empty($cost_center)) {
                    AccountTransaction::add_main_id($data->cost_center_id,round($data->total_before_tax,2),'debit',$data,'Add Purchase',1);
                }else{
                    $cost_center->update([
                        'account_id' => $data->cost_center_id,
                        'amount'     => round($data->total_before_tax,2)
                    ]);
                }
            }else{
                AccountTransaction::where('transaction_id',$data->id)
                                ->whereHas('account',function($query){
                                    $query->where('cost_center',1);
                                })->delete();
            }
            //............................................................. end cost center in purchase add *2


            // ....... ..................... add  purchase action * 3 
            $purchase_id    = ($setting)?$setting->purchase:Account::add_main('Purchases');
            AccountTransaction::add_main_id($purchase_id,round($data->total_before_tax,2),'debit',$data,'Add Purchase',NULL,$data->cost_center_id);
            //........................................................... end * 3
            

            // ............................................. add  dis  action * 4 
            if ($discount_amount > 0) {
                $purchase_discount_id    = ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
                AccountTransaction::add_main_id($purchase_discount_id,round($discount_amount,2),'credit',$data,'Add Purchase',NULL,$data->cost_center_id);
                
                if($data->cost_center_id){
                    AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'credit',$data,'Add Discount',1);
                }
            }
            //................................................................. end  * 4


            // ................................................  add  tax  * 5 
            if ($data->tax_amount > 0 ) {
                $purchase_discount_id    = ($setting)?$setting->purchase_tax:Account::add_main('Fedreal Tax Paid Vat');
                AccountTransaction::add_main_id($purchase_discount_id,round($data->tax_amount,2),'debit',$data,'Add Purchase');
                $tax = round($data->tax_amount,2);
            }
            // ...............................................................................  end  * 5 


            // ........................................................ add  supp  * 6 
            $account     =  Account::where('contact_id',$data->contact_id)->first();
            if ($account) {
                $action = \App\AccountTransaction::where('type','credit')
                                        ->where('note','Add Purchase')->where('transaction_id',$data->id)
                                        ->whereHas('account',function($query){
                                            $query->whereNotNull('contact_id');
                                        })
                                        ->first();
                if (empty($action)) {
                    $credit_data = [
                        'amount'         => round(($data->final_total - (($total!=null)?$total:0)),2),
                        'account_id'     => $account->id,
                        'type'           => 'credit',
                        'sub_type'       => 'deposit',
                        'operation_date' => $data->transaction_date,
                        'created_by'     => session()->get('user.id'),
                        'note'           => 'Add Purchase',
                        'transaction_id' => $data->id,
                        'id_delete'      => 1,
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    if($account->cost_center!=1){
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }else{
                    $action->update([
                        'amount'         => round(($data->final_total - (($total!=null)?$total:0)),2),
                        'account_id'     => $account->id,
                    ]);
                    if($account->cost_center!=1){
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }

                $actions  = \App\AccountTransaction::where('type','debit')->where('transaction_id',$data->id)
                                                    ->whereHas('account',function($query){
                                                        $query->whereNotNull('contact_id');
                                                    })->get();
                foreach ($actions as $a) {
                    $a->update([
                        'account_id' =>$account->id,
                    ]);
                }
                $credit = $data->final_total - (($total!=null)?$total:0);
                $debit  = $data->total_before_tax - $discount_amount  + $tax ;
            }
            // ...............................................................................  end  * 6 


            $type="Purchase";
            \App\Models\Entry::create_entries($data,$type,round($debit,2),round($credit,2));
            $entry  = \App\Models\Entry::where("account_transaction",$data->id)->where("state","Purchase")->first();
            ($entry) ? $entry->id :null; 
            \App\AccountTransaction::where("transaction_id",$data->id)->whereHas("account",function($query){
                                                    $query->where("cost_center",0);
                                            })->update([
                                                "entry_id" => ($entry) ? $entry->id :null
                                            ]);
            

        }
        // .F......................................................
        public static function update_purchase($data,$total=null,$old_cost=null,$old_account=null,$old_discount=null,$old_tax=null,$old_date=null)
        {
            $setting =  \App\Models\SystemAccount::where('business_id',$data->business_id)->first();
            $entry   =  \App\Models\Entry::where('account_transaction',$data->id)->first();
            //purchase account 
            $amount           =  $data->final_total - $data->tax_amount;
            $discount_amount  =  $data->discount_amount;
            
            
            if ($data->discount_type == 'percentage') {
                $discount_amount = $data->total_before_tax*($data->discount_amount/100);
            }else if ($data->discount_type == 'fixed_after_vat') {
                if($data->tax != null){
                    $discount_amount  = ($data->discount_amount/(1+ ( $data->tax->amount/100 ) )) ;
                }else{
                    $discount_amount  = ($data->discount_amount/(1+ ( 0/100 ) )) ;
                }
                if($data->exchange_price != 0){
                    $discount_amount  = $discount_amount * $data->exchange_price ;
                }
            }else{
                $discount_amount  = $data->discount_amount;
                if($data->exchange_price != 0){
                    $discount_amount  = $discount_amount * $data->exchange_price ;
                }
            }
            // $discount_amount  = round($discount_amount,config('constants.currency_precision')); 
            // $entry_id = \App\Models\Entry::orderBy("id","asc")->where("account_transaction",$data->id)->first();
            // if(empty($entry_id)){
            //         $type="Purchase";
            //         \App\Models\Entry::create_entries($data,$type,0,0);
            //         $all_item_first = \App\AccountTransaction::where("transaction_id",$data->id)
            //                                                             ->whereNull("additional_shipping_item_id")->get();
            //         $all_item = \App\AccountTransaction::where("transaction_id",$data->id)
            //                                                            ->whereHas("additional_shipping_item",function($query) { 
            //                                                                 $query->whereHas("additional_shipping",function($query) {
            //                                                                             $query->where("type",0); 
            //                                                                 });})->get();
                                                                            
            //         foreach($all_item_first as $i){
            //             $i->entry_id = $entry_id->id;
            //             $i->update();
            //         }
            //         foreach($all_item as $i){
            //             $i->entry_id = $entry_id->id;
            //             $i->update();
            //         }
            
            // }
            // ...,....................................................................... update  purchase  action  * 1 
            $purchase_id    = ($setting)?$setting->purchase:Account::add_main('Purchases');
            AccountTransaction::update_main_id($purchase_id,round($data->total_before_tax,2),'debit',$data,'Add Purchase',$data->cost_center_id,null,$old_date);
            //.................................................................................................... end * 1
        

            //.................................................................. update cost center * 2
            $cost_center = AccountTransaction::where('transaction_id',$data->id)
                                                ->whereHas('account',function($query){
                                                        $query->where('cost_center',1);
                                                    })->where("note","!=","Add Expense")
                                                    ->where("account_id",$data->cost_center_id)
                                                    ->first(); 
            if (!$data->cost_center_id) {
                AccountTransaction::where('transaction_id',$data->id)
                                        ->whereHas('account',function($query){
                                            $query->where('cost_center',1);
                                        })->where("note","!=","Add Expense")
                                        ->where("account_id",$old_cost)
                                        ->where("id_delete",1)
                                        ->delete();
            }else if ($old_cost == $data->cost_center_id) {
                if($discount_amount > 0){
                    if($old_discount != 0){

                        $purchase_discount_id  = ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
                        $cost_center_account = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->whereHas('account',function($query){
                                                                        $query->where('cost_center',1);
                                                                    })->where("type","credit")
                                                                    ->where("note",["Add Discount"])
                                                                    ->where("account_id",$data->cost_center_id)
                                                                    ->first();
                    
                        if(empty($cost_center_account)){
                            AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'credit',$data,'Add Discount',1);
                        }else{
                            $cost_center_account->update([
                                'amount'         =>  round($discount_amount,2),
                                'operation_date' =>  $data->transaction_date,
                                'entry_id'       => ($entry->id)?$entry->id:null
                            ]);
                        }
                        
                    }else{    
                        $purchase_discount_id   =  ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
                        AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'credit',$data,'Add Discount',1);
                    }
                }else{
                    if($old_discount != 0){
                        $purchase_discount_id  = ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
                        $discount_account = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->where("type","credit")
                                                                    ->where("note","Add Purchase")
                                                                    ->where("account_id",$purchase_discount_id)
                                                                    ->delete();
                        $discount_account = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->where("type","credit")
                                                                    ->where("note","Add Discount")
                                                                    ->where("account_id",$data->cost_center_id)
                                                                    ->delete();
                    } 
                }
                if(!empty($cost_center)){
                    $cost_center->update([
                        'account_id'      => $data->cost_center_id,
                        'amount'          => round($data->total_before_tax,2),
                        'operation_date'  => $data->transaction_date
                    ]);
                }
        
            }else if ($old_cost != $data->cost_center_id) {

                AccountTransaction::where('transaction_id',$data->id)
                                        ->whereHas('account',function($query){
                                            $query->where('cost_center',1);
                                        })->where("note","!=","Add Expense")
                                        ->where("account_id",$old_cost)
                                        ->where("id_delete",1)
                                        ->delete();

                AccountTransaction::add_main_id($data->cost_center_id,round($data->total_before_tax,2),'debit',$data,'Add Purchase',1);
                if($discount_amount > 0){
                    if($old_discount != 0){
                        AccountTransaction::where('transaction_id',$data->id)
                                                        ->whereHas('account',function($query){
                                                            $query->where('cost_center',1);
                                                        })->where("note","!=","Add Expense")
                                                        ->where("account_id",$old_cost)
                                                        ->where("id_delete",1)
                                                        ->delete();
                        AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'credit',$data,'Add Discount',1);
                    }else{
                        AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'credit',$data,'Add Discount',1);
                    }
                }
            } 
            //....................................................................... end * 2

            //............................................................ add  discount * 3
            if ($discount_amount > 0) {
                if($old_discount != 0){
                    $purchase_discount_id  = ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
                    $discount_account = AccountTransaction::where('transaction_id',$data->id)
                                                                ->where("type","credit")
                                                                ->where("note",["Add Purchase"])
                                                                ->where("account_id",$purchase_discount_id)
                                                                ->get();
                
                    foreach($discount_account as $dis){
                        $dis->update([
                            'amount'         =>  round($discount_amount,2),
                            'operation_date' =>  $data->transaction_date,
                            'entry_id'       =>  ($entry->id)?$entry->id:null,
                            'cs_related_id'  =>  $data->cost_center_id
                        ]);
                        if($dis->account->cost_center!=1){
                            if($old_date!=null){
                                self::nextRecords($dis->account_id,$data->business_id,$old_date);
                            }
                            // self::oldBalance($dis->id,$dis->account_id,$data->business_id,$data->transaction_date);
                            self::nextRecords($dis->account_id,$data->business_id,$data->transaction_date);
                        }                                         
                    }
                }else{    
                    $purchase_discount_id   =  ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
                    AccountTransaction::add_main_id($purchase_discount_id,round($discount_amount,2),'credit',$data,'Add Purchase',null,$data->cost_center_id,null,$old_date);
                    
                }
            }else {
                if($old_discount != 0){
                    $purchase_discount_id  = ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
                    $discount_account = AccountTransaction::where('transaction_id',$data->id)
                                                                ->where("type","credit")
                                                                ->where("note","Add Purchase")
                                                                ->where("account_id",$purchase_discount_id)
                                                                ->delete();
                    if($old_date!=null){  
                        self::nextRecords($purchase_discount_id,$data->business_id,$old_date);
                    }
                    self::nextRecords($purchase_discount_id,$data->business_id,$data->transaction_date);
                    $discount_account = AccountTransaction::where('transaction_id',$data->id)
                                                                ->where("type","credit")
                                                                ->where("note","Add Discount")
                                                                ->where("account_id",$data->cost_center_id)
                                                                ->delete();
                } 
            }
            
            //........ tax .....................................................     
            if ($data->tax_amount > 0 ) {
                if($old_tax != 0){
                    $purchase_tax_id  = ($setting)?$setting->purchase_tax:Account::add_main('Fedreal Tax Paid Vat');
                    $tax_account = AccountTransaction::where('transaction_id',$data->id)
                                                                ->where("type","debit")
                                                                ->where("note","Add Purchase")
                                                                ->where("account_id",$purchase_tax_id)
                                                                ->first();
                    if(!empty($tax_account)){
                        $tax_account->update([
                            'amount'         =>  round($data->tax_amount,2),
                            'operation_date' =>  $data->transaction_date,
                            'entry_id'       =>  ($entry->id)?$entry->id:null

                        ]);
                        if($tax_account->account->cost_center!=1){
                            if($old_date!=null){ 
                                self::nextRecords($tax_account->account_id,$data->business_id,$old_date);
                            }
                            self::nextRecords($tax_account->account_id,$data->business_id,$data->transaction_date);
                        }                                        
                    }
                }else{    
                    $purchase_tax_id   =  ($setting)?$setting->purchase_tax:Account::add_main('Fedreal Tax Paid Vat');
                    AccountTransaction::add_main_id($purchase_tax_id,round($data->tax_amount,2),'debit',$data,'Add Purchase',null,null,null,$old_date);
                }
            }else {
                if($old_tax != 0){
                    $purchase_tax_id  = ($setting)?$setting->purchase_tax:Account::add_main('Fedreal Tax Paid Vat');
                    $tax_account = AccountTransaction::where('transaction_id',$data->id)
                                                                ->where("type","debit")
                                                                ->where("note","Add Purchase")
                                                                ->where("account_id",$purchase_tax_id)
                                                                ->delete();
                    if($old_date!=null){  
                        self::nextRecords($purchase_tax_id,$data->business_id,$old_date);
                    }
                    self::nextRecords($purchase_tax_id,$data->business_id,$data->transaction_date);
                } 
            }
        
            //........ supplier ....................................................
            $account  =  Account::where('contact_id',$data->contact_id)->first();
            $old      =  Account::where('contact_id',$old_account)->first();

            $old_account_id = null;
            if(!empty($old)){
                $old_account_id = $old->id;
            }

            if ($account) {
                $account_id = $account->id;
                if($account_id == $old_account_id){
                    $action  = \App\AccountTransaction::where('type','credit')
                                                            ->whereHas('account',function($query){
                                                                $query->whereNotNull('contact_id');
                                                            })->where('note','Add Purchase')
                                                                ->where('transaction_id',$data->id)
                                                                ->where('account_id',$old_account_id)
                                                                ->first();
                    
                    if(empty($action)){
                        $credit_data = [
                            'amount'         => round(($data->final_total - (($total!=null)?$total:0)),2),
                            'account_id'     => $account->id,
                            'type'           => 'credit',
                            'sub_type'       => 'deposit',
                            'operation_date' => $data->transaction_date,
                            'created_by'     => ($user)?$user->id:session()->get('user.id'),
                            'note'           => 'Add Purchase',
                            'transaction_id' => $data->id,
                            'id_delete'      => 1,
                            'entry_id'       => ($entry->id)?$entry->id:null
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        if($account->cost_center!=1){ 
                            if($old_date!=null){   
                                self::nextRecords($account->id,$data->business_id,$old_date);
                            }
                            self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                        }  
                    }else{
                        $action->update([
                            'amount'         => round(($data->final_total - (($total!=null)?$total:0)),2),
                            'account_id'     => $account->id,
                            'operation_date' => $data->transaction_date,
                            'entry_id'       => ($entry->id)?$entry->id:null,

                        ]);
                        if($account->cost_center!=1){ 
                            if($old_date!=null){   
                                self::nextRecords($account->id,$data->business_id,$old_date);
                            }
                            self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                        }  
                    }       
                    
                
                                    
                }else if($account_id != $old_account_id){
                    if($old_account_id != null){
                        $action  = \App\AccountTransaction::where('type','credit')
                                                                ->whereHas('account',function($query){
                                                                    $query->whereNotNull('contact_id');
                                                                })->where('note','Add Purchase')
                                                                ->where('transaction_id',$data->id)
                                                                ->where('account_id',$old_account_id)
                                                                ->delete();
                        if($old_date!=null){    
                            self::nextRecords($old_account_id,$data->business_id,$old_date);
                        }
                        self::nextRecords($old_account_id,$data->business_id,$data->transaction_date);
                    }
                    $credit_data = [
                            'amount'         => round(($data->final_total - (($total!=null)?$total:0)),2),
                            'account_id'     => $account->id,
                            'type'           => 'credit',
                            'sub_type'       => 'deposit',
                            'operation_date' => $data->transaction_date,
                            'created_by'     => ($user)?$user->id:session()->get('user.id'),
                            'note'           => 'Add Purchase',
                            'transaction_id' => $data->id,
                            'id_delete'      => 1,
                            'entry_id'       => ($entry->id)?$entry->id:null

                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        if($account->cost_center!=1){ 
                            if($old_date!=null){    
                                self::nextRecords($account->id,$data->business_id,$old_date);
                            }
                            self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                        }    
                }  
                $actions  = \App\AccountTransaction::where('type','debit')
                                                        ->whereHas('account',function($query){
                                                            $query->whereNotNull('contact_id');
                                                        })
                                                        ->whereNull("payment_voucher_id")
                                                        ->where('transaction_id',$data->id)
                                                        ->get();
                foreach ($actions as $a) {
                    $a->update([
                        'account_id' => $account->id,
                        'entry_id'   => ($entry->id)?$entry->id:null
                    ]);
                }
            }
            

        }
        // .......................................................
        public static function remove_purchase($data)
        {
            
            //purchase account 
            $amount  =  $data->final_total - $data->tax_amount;
            AccountTransaction::add_main('Purchases',round($amount,2),'debit',$data);
            //15
            if ($data->tax_amount > 0 ) {
                AccountTransaction::add_main('Fedreal Tax Paid Vat',round($data->tax_amount,2),'debit',$data);
            }
            $account     =  Account::where('contact_id',$data->contact_id)->first();
            if ($account) {
                $credit_data = [
                    'amount'           => round($data->final_total,2),
                    'account_id'       => $account->id,
                    'type'             => 'credit',
                    'sub_type'         => 'deposit',
                    'operation_date'   =>  date('Y-m-d h:i:s a'),
                    'created_by'       => session()->get('user.id'),
                    'note'             => 'Add Purchase',
                    'transaction_id'   => $data->id,
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            }
            

        }
        // ...F...................................................
        public static  function return_purchase($data=null,$discount_final=null,$final_total=null,$sub_total=null,$tax=null,$user=null)
        {
            //...global information 
            $business_id = ($user)?$user->business_id:request()->session()->get("user.business_id");
            $setting = \App\Models\SystemAccount::where("business_id",$business_id)->first();
            //.... total before global discount
            $amount      =  $sub_total;

            //.... take the purchase bill returned 
            $return_transaction = Transaction::where('type', 'purchase_return')
                                                ->where('return_parent_id', $data->id)
                                                ->first();


            //.... check the cost center for total bill 
            if ($return_transaction->cost_center_id && $amount > 0) {
                $cost_center = \App\AccountTransaction::where('return_transaction_id',$return_transaction->id)
                                                            ->whereHas('account',function($query){
                                                                    $query->where('cost_center','>',0);
                                                                })
                                                            ->first();
                if (!empty($cost_center)) {
                    $cost_center->update([
                                    'amount'         => round($amount,2),
                                    'type'           => 'credit',
                                    'operation_date' => $data->transaction_date,
                                    'account_id'     => app('request')->input('cost_center_id')
                                    ]);
                }else{
                    $credit_data = [
                        'amount'                => round($amount,2),
                        'account_id'            => $return_transaction->cost_center_id,
                        'type'                  => 'credit',
                        'sub_type'              => 'deposit',
                        'operation_date'        => $data->transaction_date,
                        'created_by'            => ($user)?$user->id:session()->get('user.id'),
                        'note'                  => 'Return purchase' ,
                        'transaction_id'        => $data->id,
                        'return_transaction_id' => $return_transaction->id

                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                }
            }

            //... purchase ..
            $setting        =  \App\Models\SystemAccount::where('business_id',$data->business_id)->first();
            $return_account =  \App\AccountTransaction::where('account_id',$setting->purchase_return)
                                                        ->whereNotNull('return_transaction_id')
                                                        ->where('transaction_id',$data->id)
                                                        ->first();
                                            
            if ($return_account) {
                $return_account->update([
                    'amount'                => round($amount,2),
                    'type'                  => 'credit',
                    'operation_date'        => $data->transaction_date,
                    'note'                  => 'Return purchase ',
                    'return_transaction_id' => $return_transaction->id,
                    'cs_related_id'         => $return_transaction->cost_center_id,
                    
                ]);
                $account        = \App\Account::find($return_account->account_id); 
                if($account->cost_center!=1){
                    self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                } 
            }else {
                $credit_data = [
                    'amount'                => round($amount,2),
                    'account_id'            => $setting->purchase_return,
                    'type'                  => 'credit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => ($user)?$user->id:session()->get('user.id'),
                    'note'                  => 'Return purchase',
                    'transaction_id'        => $data->id,
                    'return_transaction_id' => $return_transaction->id,
                    'cs_related_id'         => $return_transaction->cost_center_id,
                ];
                $return_account = \App\AccountTransaction::createAccountTransaction($credit_data);
                $account        = \App\Account::find($setting->purchase_return); 
                if($account->cost_center!=1){
                    self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date); 
                }  
            }

            //.... discount
            if($discount_final != null && $discount_final != 0){
                $discount_eff = $discount_final;
                $dis_tr       = \App\AccountTransaction::where('account_id',$setting->purchase_discount)
                                                    ->where('transaction_id',$data->id)
                                                    ->where('return_transaction_id',$return_transaction->id)
                                                    ->first(); 
                
                if ($dis_tr) {
                    $dis_tr->update([
                        'amount'                => round($discount_eff,2),
                        'operation_date'        => $data->transaction_date,
                        'return_transaction_id' => $return_transaction->id,
                        'cs_related_id'         => $return_transaction->cost_center_id
                        
                    ]);
                    $account        = \App\Account::find($dis_tr->account_id); 
                    if($account->cost_center!=1){
                        self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    } 
                }else{
                    $credit_data = [
                        'amount'                => round($discount_eff,2),
                        'account_id'            => $setting->purchase_discount,
                        'type'                  => 'debit',
                        'sub_type'              => 'deposit',
                        'operation_date'        => $data->transaction_date,
                        'created_by'            =>  ($user)?$user->id:session()->get('user.id'),
                        'note'                  => 'Return purchase',
                        'transaction_id'        => $data->id,
                        'return_transaction_id' => $return_transaction->id,
                        'cs_related_id'         => $return_transaction->cost_center_id,
                    ];
                    $dis_tr = \App\AccountTransaction::createAccountTransaction($credit_data);
                    $account        = \App\Account::find($dis_tr->account_id); 
                    if($account->cost_center!=1){
                        self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    } 
                }
            }

            //....... tax .......
            $tax_tr      =  \App\AccountTransaction::where('account_id',$setting->purchase_tax)
                                                        ->where('transaction_id',$data->id)
                                                        ->where('type',"credit")
                                                        ->first();
            
            if ($tax_tr) {
                $tax_tr->update([
                    'amount'                => round($tax,2),
                    'operation_date'        => $data->transaction_date,
                    'return_transaction_id' => $return_transaction->id
                ]);
                $account        = \App\Account::find($tax_tr->account_id); 
                if($account->cost_center!=1){
                    self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                } 
            }else{
                $credit_data = [
                    'amount'                => round($tax,2),
                    'account_id'            => $setting->purchase_tax,
                    'type'                  => 'credit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => ($user)?$user->id:session()->get('user.id'),
                    'note'                  => 'Return purchase ',
                    'transaction_id'        => $data->id,
                    'return_transaction_id' => $return_transaction->id

                ];
                $tax_tr = \App\AccountTransaction::createAccountTransaction($credit_data);
                $account        = \App\Account::find($tax_tr->account_id); 
                if($account->cost_center!=1){
                    self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                } 
            }
        
            //... supplier ...
            $account        =  Account::where('contact_id',$data->contact_id)->first();
            if ($account) {
                $credit     =  \App\AccountTransaction::where('type',"debit")->where('account_id',$account->id)->where('transaction_id',$data->id)->whereNotNull('return_transaction_id')->first();
                if ($credit) {
                    $credit->update([
                        'amount'                => round($final_total,2),
                        'type'                  => 'debit',
                        'note'                  => 'Return purchase' ,
                        'operation_date'        => $data->transaction_date,
                        'return_transaction_id' => $return_transaction->id,
                        'account_id'            => $account->id
                    ]);
                    if($account->cost_center!=1){
                        self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    }
                }else{
                    $credit_data = [
                        'amount'                => round($final_total,2),
                        'account_id'            => $account->id,
                        'type'                  => 'debit',
                        'sub_type'              => 'deposit',
                        'operation_date'        => $data->transaction_date,
                        'created_by'            => ($user)?$user->id:session()->get('user.id'),
                        'note'                  => 'Return purchase ',
                        'transaction_id'        => $data->id,
                        'return_transaction_id' => $return_transaction->id
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    if($account->cost_center!=1){
                        self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    }
                }
                
            }
            if($discount_final != null && $discount_final != 0){
                //..cost center discount
                if($return_transaction->cost_center_id != null){
                    //......... cost center discount
                    $cost_discount     = \App\AccountTransaction::where("account_id",$return_transaction->cost_center_id)
                                                                        ->where('transaction_id',$data->id)
                                                                        ->where('type',"debit")
                                                                        ->where('note',"Return Discount")
                                                                        ->first();

                    $dis_        =  $discount_final;
                    
                    if(!empty($cost_discount)){
                        $cost_discount->update([
                            'amount'                 => round($dis_,2),
                            'return_transaction_id'  => $return_transaction->id,
                            'operation_date'         => $data->transaction_date,
                            'account_id'             => $return_transaction->cost_center_id

                        ]);
                    }else{
                        $credit_data = [
                            'amount'                => round($dis_,2),
                            'account_id'            => $return_transaction->cost_center_id,
                            'type'                  => 'debit',
                            'sub_type'              => 'deposit',
                            'operation_date'        => $data->transaction_date,
                            'created_by'            => ($user)?$user->id:session()->get('user.id'),
                            'note'                  => 'Return Discount',
                            'transaction_id'        => $data->id,
                            'return_transaction_id' => $return_transaction->id

                        ];
                        $tax_tr = \App\AccountTransaction::createAccountTransaction($credit_data);
                    }
                }
            }

            

        }
        // ...F.....................................................
        public static  function update_return_purchase($data=null,$discount_final=null,$final_total=null,$sub_total=null,$tax=null,$old=null,$user=null,$old_date=null)
        {

            // dd($old);
            //...global information 
            $business_id =  ($user)?$user->business_id:request()->session()->get("user.business_id");
            $setting     = \App\Models\SystemAccount::where("business_id",$business_id)->first();
            //.... total before global discount
            $amount      =  $sub_total;

            //.... take the purchase bill returned 
            $return_transaction = Transaction::where('type', 'purchase_return')
                                                ->where('return_parent_id', $data->id)
                                                ->first();


            //.... check the cost center for total bill 
            if ($return_transaction->cost_center_id && $amount > 0) {
                $cost_center = \App\AccountTransaction::where('return_transaction_id',$data->id)
                                                            ->whereHas('account',function($query){
                                                                    $query->where('cost_center','>',0);
                                                                })
                                                            ->first();
                if (!empty($cost_center)) {
                    $cost_center->update([
                                    'amount'         => round($amount,2),
                                    'operation_date' => $data->transaction_date,
                                    'type'           => 'credit',
                                    'account_id'     => $return_transaction->cost_center_id
                                    ]);
                }else{
                    $credit_data = [
                        'amount'                => round($amount,2),
                        'account_id'            => $return_transaction->cost_center_id,
                        'type'                  => 'credit',
                        'sub_type'              => 'deposit',
                        'operation_date'        => $data->transaction_date,
                        'created_by'            => ($user)?$user->id:session()->get('user.id'),
                        'note'                  => 'Return purchase' ,
                        'transaction_id'        => $data->id,
                        'return_transaction_id' => $return_transaction->id

                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                }
            }
            //... purchase ..
            $setting        =  \App\Models\SystemAccount::where('business_id',$data->business_id)->first();
            $return_account =  \App\AccountTransaction::where('account_id',$setting->purchase_return)
                                                        ->whereNotNull('return_transaction_id')
                                                        ->where('transaction_id',$data->id)
                                                        ->first();
                                            
            if ($return_account) {
                $return_account->update([
                    'amount'                => round($amount,2),
                    'type'                  => 'credit',
                    'operation_date'        => $data->transaction_date,
                    'note'                  => 'Return purchase ',
                    'return_transaction_id' => $return_transaction->id,
                    'cs_related_id'         => $return_transaction->cost_center_id,
                    
                    
                ]);
                $account = \App\Account::find($return_account->account_id);
                if($account->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($account->id,$return_transaction->business_id,$old_date);
                    }
                    self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                }
            }else {
                $credit_data = [
                    'amount'                => round($amount,2),
                    'account_id'            => $setting->purchase_return,
                    'type'                  => 'credit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => ($user)?$user->id:session()->get('user.id'),
                    'note'                  => 'Return purchase',
                    'transaction_id'        => $data->id,
                    'return_transaction_id' => $return_transaction->id,
                    'cs_related_id'         => $return_transaction->cost_center_id,
                ];
                $return_account = \App\AccountTransaction::createAccountTransaction($credit_data);
                $account = \App\Account::find($setting->purchase_return); 
                if($account->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($account->id,$return_transaction->business_id,$old_date);
                     }
                    self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                } 
            }

            //.... discount
            if($discount_final != null && $discount_final != 0){
                $discount_eff = $discount_final;
                $dis_tr       = \App\AccountTransaction::where('account_id',$setting->purchase_discount)
                                                    ->where('transaction_id',$data->id)
                                                    ->where('return_transaction_id',$return_transaction->id)
                                                    ->first(); 
                
                if ($dis_tr) {
                    $dis_tr->update([
                        'amount'                => round($discount_eff,2),
                        'operation_date'        => $data->transaction_date,
                        'return_transaction_id' => $return_transaction->id,
                        'cs_related_id'         => $return_transaction->cost_center_id,

                    ]);
                    $account = \App\Account::find($dis_tr->account_id); 
                    if($account->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account->id,$return_transaction->business_id,$old_date);
                         }
                        self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    }
                }else{
                    $credit_data = [
                        'amount'                 => $discount_eff,
                        'account_id'             => $setting->purchase_discount,
                        'type'                   => 'debit',
                        'sub_type'               => 'deposit',
                        'operation_date'         => $data->transaction_date,
                        'created_by'             => ($user)?$user->id:session()->get('user.id'),
                        'note'                   => 'Return purchase',
                        'transaction_id'         => $data->id,
                        'return_transaction_id'  => $return_transaction->id,
                        'cs_related_id'          => $return_transaction->cost_center_id,
                    ];
                    $dis_tr  = \App\AccountTransaction::createAccountTransaction($credit_data);
                    $account = \App\Account::find($setting->purchase_discount); 
                    if($account->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account->id,$return_transaction->business_id,$old_date);
                         }
                        self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    }
                }
            }
    
            //....... tax .......
            $tax_tr      =  \App\AccountTransaction::where('account_id',$setting->purchase_tax)
                                                        ->where('transaction_id',$data->id)
                                                        ->where('type',"credit")
                                                        ->first();
            
            if ($tax_tr) {
                $tax_tr->update([
                    'amount'                => round($tax,2),
                    'operation_date'        => $data->transaction_date,
                    'return_transaction_id' => $return_transaction->id,
                    
                ]);
                $account = \App\Account::find($tax_tr->account_id); 
                if($account->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($account->id,$return_transaction->business_id,$old_date);
                     }
                    self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                }
            }else{
                $credit_data = [
                    'amount'                => round($tax,2),
                    'account_id'            => $setting->purchase_tax,
                    'type'                  => 'credit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => ($user)?$user->id:session()->get('user.id'),
                    'note'                  => 'Return purchase ',
                    'transaction_id'        => $data->id,
                    'return_transaction_id' => $return_transaction->id
                ];
                $tax_tr  = \App\AccountTransaction::createAccountTransaction($credit_data); 
                $account = \App\Account::find($setting->purchase_tax); 
                if($account->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($account->id,$return_transaction->business_id,$old_date);
                     }
                    self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                }
            }
            //... supplier ...
            $account        =  Account::where('contact_id',$old->contact_id)->first();
            $account_new    =  Account::where('contact_id',$data->contact_id)->first();
            if ($account) {
                $credit     =  \App\AccountTransaction::where('type',"debit")->where('account_id',$account->id)->where('transaction_id',$data->id)->whereNull("additional_shipping_item_id")->whereNotNull('return_transaction_id')->first();
                if ($credit) {
                    
                    
                    $credit->update([
                        'amount'                => round($final_total,2),
                        'type'                  => 'debit',
                        'operation_date'        => $data->transaction_date,
                        'note'                  => 'Return purchase' ,
                        'return_transaction_id' => $return_transaction->id,
                        'account_id'            => $account_new->id

                    ]);
                    if($account->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account->id,$return_transaction->business_id,$old_date);
                         }
                        self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    }
                    if($account_new->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account_new->id,$return_transaction->business_id,$old_date);
                         }
                        self::nextRecords($account_new->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    }
                }else{
                    $credit_data = [
                        'amount'                => round($final_total,2),
                        'account_id'            => $account->id,
                        'type'                  => 'debit',
                        'sub_type'              => 'deposit',
                        'operation_date'        => $data->transaction_date,
                        'created_by'            => ($user)?$user->id:session()->get('user.id'),
                        'note'                  => 'Return purchase ',
                        'transaction_id'        => $data->id,
                        'return_transaction_id' => $return_transaction->id
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    if($account->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account->id,$return_transaction->business_id,$old_date);
                         }
                        self::nextRecords($account->id,$return_transaction->business_id,$return_transaction->transaction_date);
                    } 
                }
                
            }

            if($discount_final != null && $discount_final != 0){
                //..cost center discount
                if($return_transaction->cost_center_id != null){
                    //......... cost center discount
                    $cost_discount     = \App\AccountTransaction::where("account_id",$old->cost_center_id)
                                                                        ->where('transaction_id',$data->id)
                                                                        ->where('type',"debit")
                                                                        ->where('note',"Return Discount")
                                                                        ->first();
                    $dis_        =  $discount_final;
                    
                    if(!empty($cost_discount)){
                        $cost_discount->update([
                            'amount'                 => round($dis_,2),
                            'operation_date'         => $data->transaction_date,
                            'return_transaction_id'  => $return_transaction->id,
                            'account_id'             => $return_transaction->cost_center_id

                        ]);
                    }else{
                        $credit_data = [
                            'amount'                => round($dis_,2),
                            'account_id'            => $return_transaction->cost_center_id,
                            'type'                  => 'debit',
                            'sub_type'              => 'deposit',
                            'operation_date'        => $data->transaction_date,
                            'created_by'            => ($user)?$user->id:session()->get('user.id'),
                            'note'                  => 'Return Discount',
                            'transaction_id'        => $data->id,
                            'return_transaction_id' => $return_transaction->id

                        ];
                        $tax_tr = \App\AccountTransaction::createAccountTransaction($credit_data);
                    }
                }
            }
        }
    //  **2******************************************2** \\

    //  **3********* SUPP/CUS TRANSACTION  **********3** \\
        public static function add_suplier($data,$input,$users=null)
        {
            $account     =  Account::where('contact_id',$data->contact_id)->first();
            if ($account) {
                $credit_data = [
                    'amount'         => $input['amount'],
                    'account_id'     => $account->id,
                    'type'           => 'debit',
                    'sub_type'       => 'deposit',
                    'operation_date' => date('Y-m-d h:i:s'),
                    'created_by'     => session()->get('user.id'),
                    'note'           => 'Add Purchase Payments',
                    'transaction_id' => $data->id,
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                if($account->cost_center!=1){ 
                    self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                }
            }
        }
        // .......................................................
        public static function add_customer($data,$input)
        {
        
            $account     =  Account::where('contact_id',$data->contact_id)->first();
            $amount      =  str_replace(',','',$input['amount']);
            if ($account) {
                $credit_data = [
                    'amount'         => $amount,
                    'account_id'     => $account->id,
                    'type'           => 'credit',
                    'sub_type'       => 'deposit',
                    'operation_date' => date('Y-m-d h:i:s'),
                    'created_by'     => session()->get('user.id'),
                    'note'           => 'Add Sale Payment',
                    'transaction_id' => $data->id,
                ];
                $x =   \App\AccountTransaction::createAccountTransaction($credit_data);
                if($account->cost_center!=1){ 
                    self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                }
            }
        }
    //  **3******************************************3** \\ 
    //  ************************************************ \\

    //  **3********* SALES -  TRANSACTION  **********4** \\
        public static function add_sell_pos($data ,$pattern=null)
        {
            if($pattern!=null){
                $setting =  \App\Models\SystemAccount::where('business_id',$data->business_id)->where("pattern_id",$pattern)->first();
            }else{
                $setting =  \App\Models\SystemAccount::where('business_id',$data->business_id)->first();
            }
            
            //sales account 
                $amount           =  $data->total_before_tax;
                $discount_amount  =  $data->discount_amount;
                if ($data->discount_type == 'percentage') {
                    $discount_amount = $data->total_before_tax*($data->discount_amount/100);
                }elseif ($data->discount_type == 'fixed_after_vat') {
                    if($data->tax != null){
                        $discount_amount  = ($data->discount_amount/(1+ ( $data->tax->amount/100 ) )) ; /**here wrong #$% */
                    }else{
                        $discount_amount  = ($data->discount_amount/(1+ ( 0/100 ) )) ;
                    }
                    if($data->exchange_price != 0){
                        $discount_amount  = $discount_amount * $data->exchange_price ;
                    }
                }else {
                    $discount_amount  = $data->discount_amount;
                    if($data->exchange_price != 0){
                        $discount_amount  = $discount_amount * $data->exchange_price ;
                    }
                }
            // ... cost center ...
            $cost_center  = AccountTransaction::where('transaction_id',$data->id)
                                                    ->whereHas('account',function($query){
                                                        $query->where('cost_center',1);
                                                    })->first();
            if ($data->cost_center_id) {
                if (empty($cost_center)) {
                    AccountTransaction::add_main_id($data->cost_center_id,round($data->total_before_tax,2),'credit',$data,'Add Sale');
                }else{
                    $cost_center->update([
                        'account_id'     => $data->cost_center_id,
                        'amount'         => round($data->total_before_tax,2),
                        'operation_date' => $data->transaction_date

                    ]);
                }
            }else{
                AccountTransaction::where('transaction_id',$data->id)
                                ->whereHas('account',function($query){
                                    $query->where('cost_center',1);
                                })->delete();
            }

            // ... sale ...
            AccountTransaction::add_main_id($setting->sale,round($data->total_before_tax,2),'credit',$data,null,null,$data->cost_center_id);
            $tax = 0;
            // ... tax ....
            if ($data->tax_amount > 0 ) {
                AccountTransaction::add_main_id($setting->sale_tax,round($data->tax_amount,2),'credit',$data);
                $tax = round($data->tax_amount,2);
            }
    
            // ... discount ...
            if ($data->discount_amount > 0) {
                AccountTransaction::add_main_id($setting->sale_discount,round($discount_amount,2),'debit',$data,null,null,$data->cost_center_id);
                if($data->cost_center_id){
                    AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'debit',$data,'Add Discount',1);
                }
            }

            // ...  customer ...
            $account     =  Account::where('contact_id',$data->contact_id)->first();
            
            if ($account) {
                $action = \App\AccountTransaction::where('transaction_id',$data->id)
                                ->whereHas('account',function($query){
                                    $query->whereNotNull('contact_id');
                                })->where('type','debit')
                                            ->where('note','Add Sale')
                                            ->first();
                if ($action) {
                    $action->update([
                        'amount'     => round($data->final_total,2),
                        'account_id' => $account->id
                    ]);
                    if($account->cost_center!=1){ 
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }else{
                    $credit_data = [
                        'amount'         => round($data->final_total,2),
                        'account_id'     => $account->id,
                        'type'           => 'debit',
                        'sub_type'       => 'deposit',
                        'operation_date' => $data->transaction_date,
                        'created_by'     => $data->created_by,
                        'note'           => 'Add Sale',
                        'transaction_id' => $data->id,
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    if($account->cost_center!=1){ 
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }
                $actions = \App\AccountTransaction::where('transaction_id',$data->id)
                                ->where('type','credit')
                                ->whereHas('account',function($query){
                                    $query->whereNotNull('contact_id');
                                })->get();
                // foreach ($actions as $a) {
                //     $a->update([
                //         'account_id'=>$account->id
                //        ]);
                // }    
                $credit = $data->final_total  ;
                $debit  = $data->total_before_tax - $discount_amount  + $tax ;
                
            }else{
                AccountTransaction::add_main($data->contact->name,round($data->total_before_tax,2),'debit',$data);
                $credit = $data->final_total  ;
                $debit  = $data->total_before_tax - $discount_amount  + $tax ;
            }

            $type="Sale";
            \App\Models\Entry::create_entries($data,$type,round($debit,2),round($credit,2));
            $entry  = \App\Models\Entry::where("account_transaction",$data->id)->where("state","Sale")->first();
            ($entry) ? $entry->id :null; 
            \App\AccountTransaction::where("transaction_id",$data->id)->whereHas("account",function($query){
                                                    $query->where("cost_center",0);
                                            })->update([
                                                "entry_id" => ($entry) ? $entry->id :null
                                            ]);

        }
        // .......................................................................
        public static function update_sell_pos_($data,$total=null,$old_cost=null,$old_account=null,$old_discount=null,$old_tax=null,$pattern=null,$old_pattern=null,$old_date=null)
        {
            if($pattern!=null){
                $setting =  \App\Models\SystemAccount::where('business_id',$data->business_id)->where("pattern_id",$pattern)->first();
            }else{
                $setting =  \App\Models\SystemAccount::where('business_id',$data->business_id)->first();
            }
            $entry   =  \App\Models\Entry::where('account_transaction',$data->id)->first();
            
            //sales account  
            $tax = 0 ;
            $amount           =  $data->total_before_tax;
            $discount_amount  =  $data->discount_amount;
            if ($data->discount_type == 'percentage') {
                $discount_amount = $data->total_before_tax*($data->discount_amount/100);
            }elseif ($data->discount_type == 'fixed_after_vat') {
                if($data->tax != null){
                    $discount_amount  = ($data->discount_amount/(1+ ( $data->tax->amount/100 ) )) ; /**here wrong #$% */
                }else{
                    $discount_amount  = ($data->discount_amount/(1+ ( 0/100 ) )) ;
                }
                if($data->exchange_price != 0){
                    $discount_amount  = $discount_amount * $data->exchange_price ;
                }
            }else {
                $discount_amount  = $data->discount_amount;
                if($data->exchange_price != 0){
                    $discount_amount  = $discount_amount * $data->exchange_price ;
                }
            }

            // ... cost center ...
            $cost_center  = AccountTransaction::where('transaction_id',$data->id)
                                                    ->whereHas('account',function($query){
                                                        $query->where('cost_center',1);
                                                    })
                                                    ->where("account_id",$data->cost_center_id)
                                                    ->first();

            if (!$data->cost_center_id) {
                AccountTransaction::where('transaction_id',$data->id)
                                        ->whereHas('account',function($query){
                                            $query->where('cost_center',1);
                                        })->where("account_id",$old_cost)
                                        ->delete();
            }else if ($old_cost == $data->cost_center_id) {
                if($discount_amount > 0){
                    if($old_discount != 0){

                        $purchase_discount_id  = ($setting)?$setting->sale_discount:Account::add_main('Sales Discount');
                        $cost_center_account = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->whereHas('account',function($query){
                                                                        $query->where('cost_center',1);
                                                                    })->where("type","debit")
                                                                    ->where("note",["Add Discount"])
                                                                    ->where("account_id",$data->cost_center_id)
                                                                    ->first();
                    
                        if(empty($cost_center_account)){
                            AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'debit',$data,'Add Discount',1);
                        }else{
                            $cost_center_account->update([
                                'amount'         =>  round($discount_amount,2),
                                'operation_date' => $data->transaction_date
                            ]);
                        }
                        
                    }else{    
                        $purchase_discount_id   =  ($setting)?$setting->sale_discount:Account::add_main('Sales Discount');
                        AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'debit',$data,'Add Discount',1);
                    }
                }else{
                    if($old_discount != 0){
                        $purchase_discount_id  = ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
                        $discount_account = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->where("type","credit")
                                                                    ->where("note","Add Purchase")
                                                                    ->where("account_id",$purchase_discount_id)
                                                                    ->delete();
                        $discount_account = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->where("type","credit")
                                                                    ->where("note","Add Discount")
                                                                    ->where("account_id",$data->cost_center_id)
                                                                    ->delete();
                    } 
                }
                if(!empty($cost_center)){
                    $cost_center->update([
                        'account_id'     => $data->cost_center_id,
                        'amount'         => round($data->total_before_tax,2),
                        'operation_date' => $data->transaction_date

                    ]);
                }
        
            }else if ($old_cost != $data->cost_center_id) {

                AccountTransaction::where('transaction_id',$data->id)
                                        ->whereHas('account',function($query){
                                            $query->where('cost_center',1);
                                        })->where("account_id",$old_cost)
                                        ->delete();

                AccountTransaction::add_main_id($data->cost_center_id,$data->total_before_tax,'credit',$data,'Add Sale');
                if($discount_amount > 0){
                    if($old_discount != 0){
                        AccountTransaction::where('transaction_id',$data->id)
                                                        ->whereHas('account',function($query){
                                                            $query->where('cost_center',1);
                                                        })->where("note","!=","Add Expense")
                                                        ->where("account_id",$old_cost)
                                                        ->where("id_delete",1)
                                                        ->delete();
                        AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'debit',$data,'Add Discount',1);
                    }else{
                        AccountTransaction::add_main_id($data->cost_center_id,round($discount_amount,2),'debit',$data,'Add Discount',1);
                    }
                }

            }
    
            // ... sale ...
                if($pattern != $old_pattern){
                    $newSetting        =  \App\Models\SystemAccount::where('business_id',$data->business_id)->where("pattern_id",$old_pattern)->first();
                    $sale_id           = ($newSetting)?$newSetting->sale:null;
                    AccountTransaction::update_main_id($setting->sale,$data->total_before_tax,'credit',$data,null,$data->cost_center_id,$sale_id,$old_date);
                }else{
                    AccountTransaction::update_main_id($setting->sale,$data->total_before_tax,'credit',$data,null,$data->cost_center_id,null,$old_date);
                }

            // ... tax ....
            // if ($data->tax_amount > 0 ) {
            //     if($old_tax != 0){
            //         $sale_tax_id  = ($setting)?$setting->sale_tax:Account::add_main('Fedreal Tax Recepit Vat');
            //         $tax_account = AccountTransaction::where('transaction_id',$data->id)
            //                                                     ->where("type","credit")
            //                                                     ->where("note","Fedreal Tax Recepit Vat")
            //                                                     ->where("account_id",$sale_tax_id)
            //                                                     ->first();
            //         if(!empty($tax_account)){
            //             $tax_account->update([
            //                 'amount'         =>  round($data->tax_amount,2),
            //                 'operation_date' => $data->transaction_date
            //             ]);                                       
            //         }
            //     }else{    
            //         $sale_tax_id   =  ($setting)?$setting->sale_tax:Account::add_main('Fedreal Tax Recepit Vat');
            //         AccountTransaction::add_main_id($sale_tax_id,round($data->tax_amount,2),'credit',$data,'Fedreal Tax Recepit Vat');
            //     }
            // }else {
            //     if($old_tax != 0){
            //         $sale_tax_id  = ($setting)?$setting->sale_tax:Account::add_main('Fedreal Tax Recepit Vat');
            //         $tax_account = AccountTransaction::where('transaction_id',$data->id)
            //                                                     ->where("type","credit")
            //                                                     ->where("note","Fedreal Tax Recepit Vat")
            //                                                     ->where("account_id",$sale_tax_id)
            //                                                     ->delete();
            //     } 
            // }
            if ($data->tax_amount > 0 ) {
                    if($old_tax != 0){
                        // $sale_tax_id  = ($setting)?$setting->sale_tax:Account::add_main('Fedreal Tax Recepit Vat');
                        if($pattern != $old_pattern){
                            $newSetting        =  \App\Models\SystemAccount::where('business_id',$data->business_id)->where("pattern_id",$old_pattern)->first();
                            $sale_id           = ($newSetting)?$newSetting->sale_tax:null;
                            $sale_tax_id       = $setting->sale_tax;
                            $tax_account       = AccountTransaction::where('transaction_id',$data->id)
                                                                        ->where("type","credit")
                                                                        ->where("account_id",$sale_id)
                                                                        ->first();
                            if(!empty($tax_account)){
                                $tax_account->update([
                                    'account_id'     =>  $sale_tax_id,
                                    'amount'         =>  round($data->tax_amount,2),
                                    'operation_date' =>  $data->transaction_date
                                ]);
                                $newAccount = \App\Account::find($setting->sale_tax); 
                                $oldAccount = \App\Account::find($sale_id); 
                                if($newAccount->cost_center!=1){
                                    if($old_date != null){
                                        self::nextRecords($newAccount->id,$data->business_id,$old_date);
                                    } 
                                    self::nextRecords($newAccount->id,$data->business_id,$data->transaction_date);
                                }                                      
                                if($oldAccount->cost_center!=1){ 
                                    if($old_date != null){
                                        self::nextRecords($oldAccount->id,$data->business_id,$old_date);
                                    } 
                                    self::nextRecords($oldAccount->id,$data->business_id,$data->transaction_date);
                                }                                         
                            }
                        }else{
                            $sale_tax_id  = $setting->sale_tax;
                            $tax_account  = AccountTransaction::where('transaction_id',$data->id)
                            ->where("type","credit")
                            ->where("account_id",$sale_tax_id)
                            ->first();
                            if(!empty($tax_account)){
                                $tax_account->update([
                                    'amount'         =>  round($data->tax_amount,2),
                                    'operation_date' =>  $data->transaction_date
                                ]);
                                $newAccount = \App\Account::find($setting->sale_tax); 
                                if($newAccount->cost_center!=1){ 
                                    if($old_date != null){
                                        self::nextRecords($newAccount->id,$data->business_id,$old_date);
                                    }                                         
                                    self::nextRecords($newAccount->id,$data->business_id,$data->transaction_date);
                                }                                         
                            }
                        }
                        
                    }else{    
                        $sale_tax_id   =  $setting->sale_tax;
                        AccountTransaction::add_main_id($sale_tax_id,round($data->tax_amount,2),'credit',$data,null,null,null,null,$old_date);
                    }
                }else {
                    if($old_tax != 0){
                        if($pattern != $old_pattern){
                            $newSetting        =  \App\Models\SystemAccount::where('business_id',$data->business_id)->where("pattern_id",$old_pattern)->first();
                            $sale_id           = ($newSetting)?$newSetting->sale_tax:null;
                            $sale_tax_id       = $setting->sale_tax;
                            $tax_account       = AccountTransaction::where('transaction_id',$data->id)
                                                            ->where("type","credit")
                                                            ->where("account_id",$sale_id)
                                                            ->get();
                            foreach($tax_account as $o){
                                $account_transaction  = $o->account_id;
                                $action_date          = $o->operation_date;
                                $ac                   = \App\Account::find($account_transaction);
                                $o->delete();
                                if($ac->cost_center!=1){
                                    if($old_date != null){
                                        self::nextRecords($ac->id,$data->business_id, $old_date );
                                    }
                                    self::nextRecords($ac->id,$data->business_id,$action_date);
                                }
                            }
                        }else{
                            $sale_tax_id       = $setting->sale_tax;
                            $tax_account       = AccountTransaction::where('transaction_id',$data->id)
                                                            ->where("type","credit")
                                                            ->where("account_id",$sale_tax_id)
                                                            ->get();
                            foreach($tax_account as $o){
                                $account_transaction  = $o->account_id;
                                $action_date          = $o->operation_date;
                                $ac                   = \App\Account::find($account_transaction);
                                $o->delete();
                                if($ac->cost_center!=1){ 
                                    if($old_date != null){ 
                                        self::nextRecords($ac->id,$data->business_id,$old_date  );
                                    }
                                    self::nextRecords($ac->id,$data->business_id,$action_date);
                                }
                            }
                        }
                    } 
                }

            // ... discount ...
            if ($discount_amount > 0) {
                    if($old_discount != 0){
                        if($pattern != $old_pattern){
                            $newSetting        =  \App\Models\SystemAccount::where('business_id',$data->business_id)->where("pattern_id",$old_pattern)->first();
                            $sale_discount_id  = ($newSetting)?$newSetting->sale_discount:Account::add_main('Sales Discount');
                            $sale_discount_id_new  = ($setting)?$setting->sale_discount:Account::add_main('Sales Discount');
                            $discount_account  = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->where("type","debit")
                                                                    // ->where("note","Sales Discount")
                                                                    ->where("account_id",$sale_discount_id)
                                                                    ->get();
                                
                            foreach($discount_account as $dis){
                                $dis->update([
                                    'account_id'         =>  $sale_discount_id_new,
                                    'amount'             =>  round($discount_amount,2),
                                    'operation_date'     =>  $data->transaction_date,
                                    'cs_related_id'      =>  $data->cost_center_id,
                                ]);
                                $newAccount = \App\Account::find($sale_discount_id_new); 
                                $oldAccount = \App\Account::find($sale_discount_id); 
                                if($newAccount->cost_center!=1){ 
                                    if($old_date != null){
                                        self::nextRecords($newAccount->id,$data->business_id,$old_date);
                                    }
                                    self::nextRecords($newAccount->id,$data->business_id,$data->transaction_date);
                                }                                      
                                if($oldAccount->cost_center!=1){ 
                                    if($old_date != null){
                                        self::nextRecords($oldAccount->id,$data->business_id,$old_date);
                                    }
                                    self::nextRecords($oldAccount->id,$data->business_id,$data->transaction_date);
                                }   
                            }                                     
                        }else{
                            $sale_discount_id  = ($setting)?$setting->sale_discount:Account::add_main('Sales Discount');
                            $discount_account  = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->where("type","debit")
                                                                    ->where("account_id",$sale_discount_id)
                                                                    ->get();
                                
                            foreach($discount_account as $dis){
                                $dis->update([
                                    'amount'         =>  round($discount_amount,2),
                                    'operation_date' =>  $data->transaction_date,
                                    'cs_related_id'  =>  $data->cost_center_id,
                                ]);
                                $newAccount = \App\Account::find($sale_discount_id); 
                                if($newAccount->cost_center!=1){ 
                                    if($old_date != null){
                                        self::nextRecords($newAccount->id,$data->business_id,$old_date);
                                    }
                                    self::nextRecords($newAccount->id,$data->business_id,$data->transaction_date);
                                }    
                            }                                     
                        }
                    }else{    
                        if($pattern != $old_pattern){
                            $newSetting        =  \App\Models\SystemAccount::where('business_id',$data->business_id)->where("pattern_id",$old_pattern)->first();
                            $sale_discount_id  = ($newSetting)?$newSetting->sale_discount:Account::add_main('Sales Discount');
                             $sale_discount_id_new  = ($setting)?$setting->sale_discount:Account::add_main('Sales Discount');
                            AccountTransaction::add_main_id($sale_discount_id_new,round($discount_amount,2),'debit',$data,'Sales Discount',null,$data->cost_center_id,$sale_discount_id,$old_date);
                                                                   
                        }else{
                            $sale_discount_id   =  ($setting)?$setting->sale_discount:Account::add_main('Sales Discount');
                            AccountTransaction::add_main_id($sale_discount_id,round($discount_amount,2),'debit',$data,'Sales Discount',null,$data->cost_center_id,null,$old_date);
                        }
                    }
                }else {
                    if($old_discount != 0){
                        if($pattern != $old_pattern){
                            $newSetting        =  \App\Models\SystemAccount::where('business_id',$data->business_id)->where("pattern_id",$old_pattern)->first();
                            $sale_discount_id  = ($newSetting)?$newSetting->sale_discount:Account::add_main('Sales Discount');
                            $sale_discount_id_new  = ($setting)?$setting->sale_discount:Account::add_main('Sales Discount');
                            $discount_account_ = AccountTransaction::where('transaction_id',$data->id)
                                                                ->where("type","debit")
                                                                ->where("note","Add Discount")
                                                                ->where("account_id",$data->cost_center_id)
                                                                ->delete();
                            $discount_account      = AccountTransaction::where('transaction_id',$data->id)
                                                                ->where("type","debit")
                                                                ->where("account_id",$sale_discount_id)
                                                                ->get();
                            foreach($discount_account as $o){
                                $account_transaction  = $o->account_id;
                                $action_date          = $o->operation_date;
                                $ac                   = \App\Account::find($account_transaction);
                                $o->delete();
                                if($ac->cost_center!=1){
                                    if($old_date != null){
                                        self::nextRecords($ac->id,$data->business_id,$old_date);
                                    }
                                    self::nextRecords($ac->id,$data->business_id,$action_date);
                                }
                            }
                        }else{
                            $sale_discount_id  = ($setting)?$setting->sale_discount:Account::add_main('Sales Discount');
                            $discount_account_ = AccountTransaction::where('transaction_id',$data->id)
                                                                ->where("type","debit")
                                                                ->where("note","Add Discount")
                                                                ->where("account_id",$data->cost_center_id)
                                                                ->delete();
                            $discount_account  = AccountTransaction::where('transaction_id',$data->id)
                                                                    ->where("type","debit")
                                                                    ->where("account_id",$sale_discount_id)
                                                                    ->get();
                            foreach($discount_account as $o){
                                $account_transaction  = $o->account_id;
                                $action_date          = $o->operation_date;
                                $ac                   = \App\Account::find($account_transaction);
                                $o->delete();
                                if($ac->cost_center!=1){
                                    if($old_date != null){
                                        self::nextRecords($ac->id,$data->business_id,$old_date );
                                    }
                                    self::nextRecords($ac->id,$data->business_id,$action_date);
                                }
                            }
                        }
                    } 
                }

            // ...  customer ...
            $account     =  Account::where('contact_id',$data->contact_id)->first();
            $old         =  Account::where('contact_id',$old_account)->first();
            $old_account_id = null;

            if(!empty($old)){
                $old_account_id = $old->id;
            }

            if ($account) {
                $account_id = $account->id;
                if($account_id == $old_account_id){
                        $action = \App\AccountTransaction::where('transaction_id',$data->id)
                                                            ->whereHas('account',function($query){
                                                                    $query->whereNotNull('contact_id');
                                                            })->where('type','debit')
                                                            ->where('account_id',$old_account_id)
                                                            ->where('note','Add Sale')
                                                            ->first();
                        
                        if(empty($action)){
                            $credit_data = [
                                'amount'         => round($data->final_total,2)  ,
                                'account_id'     => $account->id,
                                'type'           => 'debit',
                                'sub_type'       => 'deposit',
                                'operation_date' => $data->transaction_date,
                                'created_by'     => session()->get('user.id'),
                                'note'           => 'Add Sale',
                                'transaction_id' => $data->id,
                                'entry_id'       => ($entry->id)?$entry->id:null,
                                
                            ];
                            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                            if($account->cost_center!=1){ 
                                if($old_date != null){
                                    self::nextRecords($account->id,$data->business_id,$old_date );
                                }
                                self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                            }    
                        }else{
                            $action->update([
                                'amount'          => round($data->final_total,2),
                                'account_id'      => $account->id,
                                'operation_date'  => $data->transaction_date,
                            ]);
                            if($account->cost_center!=1){ 
                                if($old_date != null){
                                    self::nextRecords($account->id,$data->business_id,$old_date );
                                }                                            
                                self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                            }                                      
                            if($old->cost_center!=1){ 
                                if($old_date != null){
                                    self::nextRecords($old->id,$data->business_id,$old_date );
                                }   
                                self::nextRecords($old->id,$data->business_id,$data->transaction_date);
                            }   
                        }   
                    }else if($account_id != $old_account_id) {
                        if($old_account_id != null){
                            $action  = \App\AccountTransaction::where('type','debit')
                            ->whereHas('account',function($query){
                                $query->whereNotNull('contact_id');
                            })->where('note','Add Sale')
                            ->where('transaction_id',$data->id)
                            ->where('account_id',$old_account_id)
                            ->get();
                            foreach($action as $o){
                                $account_transaction  = $o->account_id;
                                $action_date          = $o->operation_date;
                                $ac                   = \App\Account::find($account_transaction);
                                $o->delete();
                                if($ac->cost_center!=1){
                                    if($old_date != null){
                                        self::nextRecords($ac->id,$data->business_id,$old_date );
                                    }
                                    self::nextRecords($ac->id,$data->business_id,$action_date);
                                }
                            }
                        }
                    $credit_data = [
                        'amount'         => round($data->final_total,2) ,
                        'account_id'     => $account->id,
                        'type'           => 'debit',
                        'sub_type'       => 'deposit',
                        'operation_date' => $data->transaction_date,
                        'created_by'     => session()->get('user.id'),
                        'note'           => 'Add Sale',
                        'transaction_id' => $data->id,
                        'entry_id'       => ($entry->id)?$entry->id:null,
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    if($account->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account->id,$data->business_id,$old_date );
                        }
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }

                $actions = \App\AccountTransaction::where('transaction_id',$data->id)
                                                    ->where('type','credit')
                                                    ->whereHas('account',function($query){
                                                        $query->whereNotNull('contact_id');
                                                    })->get();
                foreach ($actions as $a) {
                    $a->update([
                        'account_id'=>$account->id
                    ]);
                } 
            }

    

        }
        // ...F...................................................................
        public static function return_sales($data=null,$discount_final=null,$final_total=null,$sub_total=null,$tax_=null,$users=null)
        {
            $parent             =  \App\Transaction::find($data->id);
            $account            =  Account::where('contact_id',$parent->contact_id)->first();
            $business_id        =  request()->session()->get('user.business_id');
            $setting            =  \App\Models\SystemAccount::where('business_id',$parent->business_id)->where('pattern_id',$parent->pattern_id)->first();
            $amount             =  $sub_total;
            $sale_amount        =  $sub_total;
            // .... sale 
            $return_transaction =   Transaction::where('type', 'sell_return')
                                                        ->where('return_parent_id', $parent->id)
                                                        ->first();
            $return_account     =  \App\AccountTransaction::where('account_id',$setting->sale_return)
                                                        ->where('transaction_id',$parent->id)
                                                        ->whereNotNull('return_transaction_id')
                                                        ->first();
            if ($return_account) {
                $return_account->update([
                    'amount'                => round($sale_amount,2),
                    'type'                  => 'debit',
                    'operation_date'        => $data->transaction_date,
                    'note'                  => 'Return sales' ,
                    'return_transaction_id' => ($return_transaction)?$return_transaction->id:NULL,
                    'cs_related_id'         => $return_transaction->cost_center_id
                ]);
                $saleAccount            =  \App\Account::find($setting->sale_return);
                if($saleAccount->cost_center!=1){
                    self::nextRecords($saleAccount->id,$data->business_id,$data->transaction_date);
                }
            }else {
                $credit_data = [
                    'amount'                => round($sale_amount,2),
                    'account_id'            => $setting->sale_return,
                    'type'                  => 'debit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => ($users!=null)?$users:session()->get('user.id'),
                    'note'                  => 'Return sales',
                    'transaction_id'        => $parent->id,
                    'return_transaction_id' => $return_transaction->id,
                    'cs_related_id'         => $return_transaction->cost_center_id
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                $saleAccount    = \App\Account::find($setting->sale_return);
                if($saleAccount->cost_center!=1){
                    self::nextRecords($saleAccount->id,$data->business_id,$data->transaction_date);
                }
            }
    
            # .. customer 
            $client_amount  =  $final_total;
            if ($account) {
                $credit     =  \App\AccountTransaction::where("transaction_id",$parent->id)->whereNotNull('return_transaction_id')->where('account_id',$account->id)->first();
                if ($credit) {
                    $credit->update([
                        'amount'         => round($client_amount,2),
                        'operation_date' => $data->transaction_date,
                        'note'           => 'Return sales',
                    ]);
                    if($account->cost_center!=1){
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }else{ 
                    $credit_data = [
                        'amount'                => round($client_amount,2),
                        'account_id'            => $account->id,
                        'type'                  => 'credit',
                        'sub_type'              => 'deposit',
                        'operation_date'        => $data->transaction_date,
                        'created_by'            => ($users!=null)?$users:session()->get('user.id'),
                        'note'                  => 'Return sales',
                        'transaction_id'        => $parent->id,
                        'return_transaction_id' => $return_transaction->id
    
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    if($account->cost_center!=1){
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }
            }

            //...  tax
            $tax    =  $tax_;
            $tax_account_id  = ($setting)?$setting->sale_tax:Account::add_main('Return Sales Tax');
            $tax_tr =  \App\AccountTransaction::where('transaction_id',$parent->id)->whereNotNull('return_transaction_id')
                                        ->where('note','Return Sales Tax')->first();
            if ($tax_tr) {
                $tax_tr->update([
                    'amount'         => round($tax,2),
                    'operation_date' => $data->transaction_date,
                    'type'           => 'debit',
                ]);
                $taxAccount    =  \App\Account::find($tax_account_id);
                if($taxAccount->cost_center!=1){
                    self::nextRecords($taxAccount->id,$data->business_id,$data->transaction_date);
                }
            }else{
                $credit_data = [
                    'amount'                => round($tax,2),
                    'account_id'            => $setting->sale_tax,
                    'type'                  => 'debit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => ($users!=null)?$users:session()->get('user.id'),
                    'note'                  => 'Return Sales Tax',
                    'transaction_id'        => $parent->id,
                    'return_transaction_id' => $return_transaction->id
    
                ];
                $tax_tr = \App\AccountTransaction::createAccountTransaction($credit_data);
                $taxAccount    =  \App\Account::find($tax_account_id);
                if($taxAccount->cost_center!=1){
                    self::nextRecords($taxAccount->id,$data->business_id,$data->transaction_date);
                }
            }

            //discount_effect  
            $dis_effect      =   $discount_final;
            $dis_transaction =   \App\AccountTransaction::where('account_id',$setting->sale_discount)->whereNotNull('return_transaction_id')
                                                                    ->where('transaction_id',$parent->id)
                                                                    ->first();
            if ($dis_transaction) {
                $dis_transaction->update([
                    'amount'                => round($dis_effect,2),
                    'type'                  => 'credit',
                    'operation_date'        => $data->transaction_date,
                    'note'                  => 'Return sales',
                    'return_transaction_id' => ($return_transaction)?$return_transaction->id:NULL,
                    'cs_related_id'         => $return_transaction->cost_center_id
                ]);
                $disAccount    =  \App\Account::find($setting->sale_discount);
                if($disAccount->cost_center!=1){
                    self::nextRecords($disAccount->id,$data->business_id,$data->transaction_date);
                }
            }else{
                $credit_data = [
                    'amount'                => round($dis_effect,2),
                    'account_id'            => $setting->sale_discount,
                    'type'                  => 'credit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => ($users!=null)?$users:session()->get('user.id'),
                    'note'                  => 'Return sales',
                    'transaction_id'        => $parent->id,
                    'return_transaction_id' => $return_transaction->id,
                    'cs_related_id'         => $return_transaction->cost_center_id

                ];
                $dis_transaction = \App\AccountTransaction::createAccountTransaction($credit_data);
                $disAccount      =  \App\Account::find($setting->sale_discount);
                if($disAccount->cost_center!=1){
                    self::nextRecords($disAccount->id,$data->business_id,$data->transaction_date);
                }
            }

            //.. Cost Center 
            if ($return_transaction->cost_center_id && $amount > 0) {
                $cost_center = \App\AccountTransaction::where('transaction_id',$parent->id)->whereNoTNull('return_transaction_id')
                                            ->whereHas('account',function($query){
                                                $query->where('cost_center','>',0);
                                            })
                                            ->first();
                    if ($cost_center) {
                                $cost_center->update([
                                        'amount'         => round($amount,2),
                                        'type'           => 'debit',
                                        'operation_date' => $data->transaction_date,
                                        'account_id'     => app('request')->input('cost_center_id')
                                            ]);
                    }else{
                            $credit_data = [
                                'amount'                 => round($amount,2),
                                'account_id'             => $return_transaction->cost_center_id,
                                'type'                   => 'debit',
                                'sub_type'               => 'deposit',
                                'operation_date'         => $data->transaction_date,
                                'created_by'             => ($users!=null)?$users:session()->get('user.id'),
                                'note'                   => 'Return sales',
                                'transaction_id'         => $parent->id,
                                'return_transaction_id'  => $return_transaction->id
                                
                            ];
                            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    }
            }
            if($discount_final != null && $discount_final != 0){
                # ..cost center discount
                if($return_transaction->cost_center_id != null){
                    # ......... cost center discount
                    $cost_discount     = \App\AccountTransaction::where("account_id",$return_transaction->cost_center_id)
                                                                        ->where('transaction_id',$parent->id)
                                                                        ->where('type',"credit")
                                                                        ->where('note',"Return Discount")
                                                                        ->first();
                    $dis_              =  $discount_final;
                    
                    if(!empty($cost_discount)){
                        $cost_discount->update([
                            'amount'                => round($dis_,2),
                            'operation_date'        => $data->transaction_date,
                            'return_transaction_id' => $return_transaction->id

                        ]);
                    }else{
                        $credit_data = [
                            'amount'                => round($dis_,2),
                            'account_id'            => $return_transaction->cost_center_id,
                            'type'                  => 'credit',
                            'sub_type'              => 'deposit',
                            'operation_date'        => $data->transaction_date,
                            'created_by'            => ($users!=null)?$users:session()->get('user.id'),
                            'note'                  => 'Return Discount',
                            'transaction_id'        => $parent->id,
                            'id_delete'             => 1,
                            'return_transaction_id' => $return_transaction->id
    
                        ];
                        $tax_tr = \App\AccountTransaction::createAccountTransaction($credit_data);
                    }
                }
            }
            $type="SReturn";
            \App\Models\Entry::create_entries($return_transaction,$type);
            
        }
        // ..F.....................................................................
        public static function update_return_sales($data=null,$discount_final=null,$final_total=null,$sub_total=null,$tax_=null,$old=null,$old_date=null)
        {
            $parent             =  \App\Transaction::find($data->id);
            $account            =   Account::where('contact_id',$old->contact_id)->first();
            $account_new        =   Account::where('contact_id',$parent->contact_id)->first();
            $business_id        =   request()->session()->get('user.business_id');
            $setting            =  \App\Models\SystemAccount::where('business_id',$parent->business_id)->where('pattern_id',$parent->pattern_id)->first();            
            $amount             =  $sub_total;
            $sale_amount        =  $sub_total;
            // .... sale 
            $return_transaction = Transaction::where('type', 'sell_return')
                                                        ->where('return_parent_id', $parent->id)
                                                        ->first();
            $return_account     =  \App\AccountTransaction::where('account_id',$setting->sale_return)
                                                        ->where('transaction_id',$parent->id)
                                                        ->whereNotNull('return_transaction_id')
                                                        ->first();
            if ($return_account) {
                $return_account->update([
                    'amount'                 => round($sale_amount,2),
                    'type'                   => 'debit',
                    'operation_date'         => $data->transaction_date,
                    'note'                   => 'Return sales' ,
                    'return_transaction_id'  => ($return_transaction)?$return_transaction->id:NULL,
                    'cs_related_id'          => $return_transaction->cost_center_id

                ]);
                $saleAccount            =  \App\Account::find($setting->sale_return);
                if($saleAccount->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($saleAccount->id,$data->business_id,$old_date);
                     }
                    self::nextRecords($saleAccount->id,$data->business_id,$data->transaction_date);
                }
            }else {
                $credit_data = [
                    'amount'                 => round($sale_amount,2),
                    'account_id'             => $setting->sale_return,
                    'type'                   => 'debit',
                    'sub_type'               => 'deposit',
                    'operation_date'         => $data->transaction_date,
                    'created_by'             => session()->get('user.id'),
                    'note'                   => 'Return sales',
                    'transaction_id'         => $parent->id,
                    'return_transaction_id'  => $return_transaction->id,
                    'cs_related_id'          => $return_transaction->cost_center_id

                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                $saleAccount            =  \App\Account::find($setting->sale_return);
                if($saleAccount->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($saleAccount->id,$data->business_id,$old_date);
                     }
                    self::nextRecords($saleAccount->id,$data->business_id,$data->transaction_date);
                }
            }

            ///.. customer 
            $client_amount  =  $final_total;
            if ($account) {
                $credit     =  \App\AccountTransaction::where("transaction_id",$parent->id)->whereNotNull('return_transaction_id')->where('account_id',$account->id)->first();
                if ($credit) {
                    $credit->update([
                        'amount'                => round($client_amount,2),
                        'operation_date'        => $data->transaction_date,
                        'note'                  => 'Return sales',
                        'account_id'            => $account_new->id,
                    ]);
                    if($account_new->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account_new->id,$data->business_id,$old_date);
                         }
                        self::nextRecords($account_new->id,$data->business_id,$data->transaction_date);
                    }
                    if($account->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account->id,$data->business_id,$old_date);
                         }
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }else{ 
                    $credit_data = [
                        'amount'                => round($client_amount,2),
                        'account_id'            => $account->id,
                        'type'                  => 'credit',
                        'sub_type'              => 'deposit',
                        'operation_date'        => $data->transaction_date,
                        'created_by'            => session()->get('user.id'),
                        'note'                  => 'Return sales',
                        'transaction_id'        => $parent->id,
                        'return_transaction_id' => $return_transaction->id
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    if($account->cost_center!=1){
                        if($old_date != null){
                            self::nextRecords($account->id,$data->business_id,$old_date);
                         }
                        self::nextRecords($account->id,$data->business_id,$data->transaction_date);
                    }
                }
            }

            //...  tax
            $tax    =  $tax_;
            $tax_account_id  = ($setting)?$setting->sale_tax:Account::add_main('Return Sales Tax');
            $tax_tr =  \App\AccountTransaction::where('transaction_id',$parent->id)->whereNotNull('return_transaction_id')
                                        ->where('note','Return Sales Tax')->first();
            if ($tax_tr) {
                $tax_tr->update([
                    'amount'                => round($tax,2),
                    'operation_date'        => $data->transaction_date,
                    'type'                  => 'debit',
                ]);
                $TAXAccount            =  \App\Account::find($tax_account_id);
                if($TAXAccount->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($TAXAccount->id,$data->business_id,$old_date);
                     }
                    self::nextRecords($TAXAccount->id,$data->business_id,$data->transaction_date);
                }
            }else{
                $credit_data = [
                    'amount'                => round($tax,2),
                    'account_id'            => $setting->sale_tax,
                    'type'                  => 'debit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => session()->get('user.id'),
                    'note'                  => 'Return Sales Tax',
                    'transaction_id'        => $parent->id,
                    'return_transaction_id' => $return_transaction->id

                ];
                $tax_tr = \App\AccountTransaction::createAccountTransaction($credit_data);
                $TAXAccount         = \App\Account::find($tax_account_id);
                if($TAXAccount->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($TAXAccount->id,$data->business_id,$old_date);
                     }
                    self::nextRecords($TAXAccount->id,$data->business_id,$data->transaction_date);
                }
            }

            //discount_effect  
            $dis_effect      =   $discount_final;
            $dis_transaction =   \App\AccountTransaction::where('account_id',$setting->sale_discount)->whereNotNull('return_transaction_id')
                                                                    ->where('transaction_id',$parent->id)
                                                                    ->first();
            if ($dis_transaction) {
                $dis_transaction->update([
                    'amount'                => round($dis_effect,2),
                    'type'                  => 'credit',
                    'operation_date'        => $data->transaction_date,
                    'note'                  => 'Return sales',
                    'return_transaction_id' => ($return_transaction)?$return_transaction->id:NULL,
                    'cs_related_id'         => $return_transaction->cost_center_id

                ]);
                $disAccount      =  \App\Account::find($setting->sale_discount);
                if($disAccount->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($disAccount->id,$data->business_id,$old_date);
                     }
                    self::nextRecords($disAccount->id,$data->business_id,$data->transaction_date);
                }
            }else{
                $credit_data = [
                    'amount'                => round($dis_effect,2),
                    'account_id'            => $setting->sale_discount,
                    'type'                  => 'credit',
                    'sub_type'              => 'deposit',
                    'operation_date'        => $data->transaction_date,
                    'created_by'            => session()->get('user.id'),
                    'note'                  => 'Return sales',
                    'transaction_id'        => $parent->id,
                    'return_transaction_id' => $return_transaction->id,
                    'cs_related_id'         => $return_transaction->cost_center_id
                ];
                $dis_transaction = \App\AccountTransaction::createAccountTransaction($credit_data);
                $disAccount      = \App\Account::find($setting->sale_discount);
                if($disAccount->cost_center!=1){
                    if($old_date != null){
                        self::nextRecords($disAccount->id,$data->business_id,$old_date);
                     }
                    self::nextRecords($disAccount->id,$data->business_id,$data->transaction_date);
                } 
            }

            //.. Cost Center 
            if ($return_transaction->cost_center_id && $amount > 0) {
                $cost_center = \App\AccountTransaction::where('transaction_id',$parent->id)->whereNoTNull('return_transaction_id')
                                            ->whereHas('account',function($query){
                                                $query->where('cost_center','>',0);
                                            })
                                            ->first();
                    
                    if ($cost_center) {
                                $cost_center->update([
                                        'amount'         => round($amount,2),
                                        'operation_date' => $data->transaction_date,
                                        'type'           => 'debit',
                                        'account_id'     => $return_transaction->cost_center_id]);
                    }else{
                            $credit_data = [
                                'amount'                 => round($amount,2),
                                'account_id'             => $return_transaction->cost_center_id,
                                'type'                   => 'debit',
                                'sub_type'               => 'deposit',
                                'operation_date'         => $data->transaction_date,
                                'created_by'             => session()->get('user.id'),
                                'note'                   => 'Return sales',
                                'transaction_id'         => $parent->id,
                                'return_transaction_id'  => $return_transaction->id
                                

                            ];
                            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    }
            }
            
            if($discount_final != null && $discount_final != 0){
                //..cost center discount
                if($return_transaction->cost_center_id != null){
                    //......... cost center discount
                    $cost_discount     = \App\AccountTransaction::where("account_id",$old->cost_center_id)
                                                                        ->where('transaction_id',$parent->id)
                                                                        ->where('type',"credit")
                                                                        ->where('note',"Return Discount")
                                                                        ->first();
                
                    $dis_              =  $discount_final;
                    
                    if(!empty($cost_discount)){
                        $cost_discount->update([
                            'amount'                 => round($dis_,2),
                            'operation_date'         => $data->transaction_date,
                            'return_transaction_id'  => $return_transaction->id,
                            'account_id'             => $return_transaction->cost_center_id

                        ]);
                    }else{
                        $credit_data = [
                            'amount'                 => round($dis_,2),
                            'account_id'             => $return_transaction->cost_center_id,
                            'type'                   => 'credit',
                            'sub_type'               => 'deposit',
                            'operation_date'         => $data->transaction_date,
                            'created_by'             => session()->get('user.id'),
                            'note'                   => 'Return Discount',
                            'transaction_id'         => $parent->id,
                            'id_delete'              => 1,
                            'return_transaction_id'  => $return_transaction->id

                        ];
                        $tax_tr = \App\AccountTransaction::createAccountTransaction($credit_data);
                    }
                } 
            }
            if($return_transaction->cost_center_id == null){
                $cost_center = \App\AccountTransaction::where('transaction_id',$parent->id)->whereNoTNull('return_transaction_id')
                                        ->whereHas('account',function($query){
                                            $query->where('cost_center','>',0);
                                        })
                                        ->first();
                if(!empty($cost_center)){
                     $cost_center->delete();
                }
                
                $cost_discount     = \App\AccountTransaction::where("account_id",$old->cost_center_id)
                                                                        ->where('transaction_id',$parent->id)
                                                                        ->where('type',"credit")
                                                                        ->where('note',"Return Discount")
                                                                        ->get();
                foreach($cost_discount as $it){
                        $it->delete();
                }
            }
            $type="SReturn";
            \App\Models\Entry::create_entries($return_transaction,$type);
            
        }
        // .........................................................................
        public static function update_sell_pos($data,$old_contact)
        {
            $setting =  \App\Models\SystemAccount::where('business_id',$data->business_id)->first();
            //sales account 
            $amount  =  $data->final_total - $data->tax_amount;
            AccountTransaction::update_main('SALES',round($amount,2),'credit',$data);
            
            if ($data->tax_amount > 0 ) {
                AccountTransaction::update_main('Fedreal Tax Recepit Vat',round($data->tax_amount,2),'credit',$data);
            }
            $account         =  Account::where('contact_id',$data->contact_id)->first();
            $old_account     =  Account::where('contact_id',$old_account)->first();
            $pay             =  AccountTransaction::where('transaction_id',$data->id)
                                        ->where('acccount_id',$old_account)->first();
            if ($pay) {
                $pay->update([
                    'amount'     => round($data->final_total,2),
                    'account_id' => $account->id,
                ]);
            }else{
                if ($account) {
                    $credit_data = [
                        'amount'         => round($data->final_total,2),
                        'account_id'     => $account->id,
                        'type'           => 'debit',
                        'sub_type'       => 'deposit',
                        'operation_date' =>  date('Y-m-d h:i:s a'),
                        'created_by'     => session()->get('user.id'),
                        'note'           => 'Add Sale',
                        'transaction_id' => $data->id,
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                }
            }
        }
    //  **4******************************************4** \\

    //  **5**** COST CENTER -  TRANSACTION  *********5** \\
        // purchase add .. cost center main
        public static function add_main_id($id,$amount,$state,$data,$text=NULL,$id_delete=null,$cost_center=null ,$old_account = null,$old_date = null)
        {
                
                
                //purchase account 
                $business_id = $data->business_id;
                $account     = Account::find($id); 
                $action      = \App\AccountTransaction::where('transaction_id',$data->id)->whereNull("check_id")->whereNull("payment_voucher_id")->get();
                $entry_id    = null ;

                foreach($action as $ac){
                    if($ac->entry_id != null){
                        $entry_id = $ac->entry_id;
                        break;
                    }
                }
                $credit_data = [
                    'amount'         => round($amount,2),
                    'account_id'     => $id,
                    'type'           => $state,
                    'sub_type'       => 'deposit',
                    'operation_date' => $data->transaction_date,
                    'created_by'     => $data->created_by,
                    'note'           => $text??$account->name,
                    'transaction_id' => $data->id,
                    'id_delete'      => $id_delete,
                    'entry_id'       => $entry_id,
                    'cs_related_id'  => $cost_center,
                ];
                
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                if($account->cost_center!=1){ 
                    if($old_date!=null){ 
                        self::nextRecords($id,$business_id,$old_date);
                    }
                    self::nextRecords($id,$business_id,$data->transaction_date);
                }
            
        }
        // ..................................................................
        public static function add_main($type,$amount,$state,$data,$text=null,$old_date=null)
        {
            //purchase account 
            $business_id                  = request()->session()->get('user.business_id');
            $account                      = \App\Account::where('name',$type)->where('business_id',$business_id)->first();
            if (empty($account)) {
                $account                  =  new \App\Account;
                $account->name            =  $type;
                $account->business_id     =  $business_id;
                $account->name            =  $type;
                $account->account_number  =  '00000'.$type;
                $account->save();
            } 
            $credit_data = [
                'amount'                  => round($amount,2),
                'account_id'              => $account->id,
                'type'                    => $state,
                'sub_type'                => 'deposit',
                'operation_date'          => date('Y-m-d h:i:s '),
                'created_by'              => session()->get('user.id'),
                'note'                    => $text??$type,
                'transaction_id'          => $data->id,
            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            if($account->cost_center!=1){ 
                if($old_date!=null){  
                    self::nextRecords($account->id,$account->business_id,$old_date);
                }
                self::nextRecords($account->id,$account->business_id,date('Y-m-d h:i:s'));
            }
        }
        // ...................................................................
        // updates
        public static function update_main_id($id,$amount,$state,$data,$text=NULL,$cost_center=NULL,$old_account=null,$old_date=null)
        {
            //purchase account 
            $business_id = request()->session()->get('user.business_id');
            if($old_account != null){
                $action      = \App\AccountTransaction::where('transaction_id',$data->id)
                                        ->where('account_id',$old_account)->first();
                                        // ->get();
                 
                
                $entry       = \App\Models\Entry::where('account_transaction',$data->id)->first();
                if(!empty($action)){   
                    $action->update([
                        'account_id'      => $id,
                        'amount'          => round($amount,2),
                        'operation_date'  => $data->transaction_date,
                        'entry_id'        => ($entry)?$entry->id:null,
                        'cs_related_id'   => $cost_center
                    ]);
                    if($action->account->cost_center!=1){ 
                        if($old_date!=null){  
                            self::nextRecords($id,$business_id,$old_date);
                        }
                        self::nextRecords($id,$business_id,$data->transaction_date);
                    }
                    $oldAccount = \App\Account::find($old_account);
                    if($oldAccount->cost_center!=1){ 
                        if($old_date!=null){   
                            self::nextRecords($oldAccount->id,$business_id,$old_date);
                        }
                        self::nextRecords($oldAccount->id,$business_id,$data->transaction_date);
                    }
                }
            }else{
                
                $action      = \App\AccountTransaction::where('transaction_id',$data->id)
                                                        ->where('account_id',$id)->first();
                
                $entry       = \App\Models\Entry::where('account_transaction',$data->id)->first();
                if(!empty($action)){   
                    $action->update([
                        'amount'          => round($amount,2),
                        'operation_date'  => $data->transaction_date,
                        'entry_id'        => ($entry)?$entry->id:null,
                        'cs_related_id'   => $cost_center
                    ]);
                    if($action->account->cost_center!=1){ 
                        if($old_date!=null){   
                            self::nextRecords($id,$business_id,$old_date);
                        }
                        self::nextRecords($id,$business_id,$data->transaction_date);
                    }
                }
            }
        }
        // ....................................................................
        // SHIPPING
        public static function add_shipp_id($id,$amount,$state,$data,$text=NULL,$date=null,$id_delete=null,$return=null,$old_date=null)
        {
            
            //shipping account 
            $business_id = ($user != null)?$user->business_id:request()->session()->get('user.business_id');
            $account     = Account::find($id); 
            $credit_data = [
                'amount'                      => round(doubleVal($amount),2) ,
                'account_id'                  => $id ,
                'type'                        => $state ,
                'sub_type'                    => 'deposit' ,
                'operation_date'              => ($date != null)? $date : date('Y-m-d h:i:s') ,
                'created_by'                  => ($user != null)?($user->id):session()->get('user.id'),
                'note'                        => $text??$account->name ,
                'transaction_id'              => $data->id ,
                'additional_shipping_item_id' => $id_delete ,
                'id_delete'                   => $id_delete ,
                'return_transaction_id'       => $return


            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            if($account->cost_center!=1){ 
                if($old_date!=null){   
                    self::nextRecords($id,$business_id,$old_date); 
                }
                self::nextRecords($id,$business_id,$data->transaction_date);
            }
            
        }
        // .....................................................................
        // SHIPPING
        public static function update_shipp_id($id,$amount,$state,$data,$text=NULL,$date=null,$id_delete=null,$old_date =null)
        {
            
            //shipping account 
            $business_id = request()->session()->get('user.business_id');
            $account     = Account::find($id); 
            $shipping    = AccountTransaction::where('transaction_id',$data->id)
                                                        ->whereHas('account',function($query){
                                                            $query->where('cost_center',1);
                                                        })->where("note","!=","Add Expense")
                                                        ->where("account_id",$id)
                                                        ->where("id_delete",1)
                                                        ->first();
            if($shipping){
                $shipping->delete();
            }
            
            $credit_data = [
                'amount'                      => round($amount,2) ,
                'account_id'                  => $id ,
                'type'                        => $state ,
                'sub_type'                    => 'deposit' ,
                'operation_date'              =>  ($date != null)? $date : date('Y-m-d') ,
                'created_by'                  => session()->get('user.id'),
                'note'                        => $text??$account->name ,
                'transaction_id'              => $data->id ,
                'additional_shipping_item_id' => $id_delete ,
                'id_delete'                   => $id_delete ,
            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            if($account->cost_center!=1){ 
                if($old_date!=null){   
                    self::nextRecords($id,$business_id,$old_date); 
                }
                self::nextRecords($id,$business_id,$data->transaction_date);
            }
            
        }
    //  ************************************************ \\
    
    //  **6****** MANUFACTURING TRANSACTION *********6** \\
        public static function production_entry($transaction,$final,$total_before,$Cost_extra=null,$old=null){

            $setting                = \App\Models\SystemAccount::where('business_id',$transaction->business_id)->first();
            $accounts               = \Modules\Manufacturing\Entities\MfgRecipe::production_accounts();
            $purchase_id            = ($setting)?$setting->purchase:\App\Account::add_main('Purchases');
            $account_production     = $accounts[0];
            $account_profit         = $accounts[1];
            $old_profit             = null;  
            $old_account            = null;

            if($old!=null){
                foreach($old as $it){
                    if($it->type == "credit"){
                        if(($it->account_id != $account_profit) && ($it->account_id != $purchase_id)){
                            $old_profit             = $it->account_id ;
                            $new_account            = Account::find($old_profit); 
                            $old_account            = Account::find($account_profit); 
                            $old_purchase_account   = Account::find($purchase_id); 
                            if($new_account->cost_center!=1){  self::nextRecords($new_account->id,$new_account->business_id,$it->operation_date); }
                            if($old_account->cost_center!=1){  self::nextRecords($old_account->id,$old_account->business_id,$it->operation_date); }
                            if($old_purchase_account->cost_center!=1){  self::nextRecords($old_purchase_account->id,$old_purchase_account->business_id,$it->operation_date); }
                        }
                    }
                    if($it->type == "debit"){
                        if(($it->account_id != $account_production)){
                            $old_account  =  $it->account_id;
                            $new_account      = Account::find($old_account); 
                            $old_account      = Account::find($account_production); 
                            if($new_account->cost_center!=1){  self::nextRecords($new_account->id,$new_account->business_id,$it->operation_date); }
                            if($old_account->cost_center!=1){  self::nextRecords($old_account->id,$old_account->business_id,$it->operation_date); } 
                        }
                    }
                }
            }  
            
            $line     = \App\TransactionSellLine::where("transaction_id",$transaction->id)->select(\DB::raw("SUM(quantity*unit_price) as total"))->first();
            if(!empty($line)){
                    $total_po = round($line->total,2);
            }else{
                    $total_po = 0;
            } 

            # mfg account
            if($old_account!=null){
                if($old_account == $account_production){
                    AccountTransaction::mfg_account($transaction,$account_production,$total_po);
                }else{
                    $mfg    =   AccountTransaction::where("account_id",$old_account)
                                                    ->whereHas("transaction",function($query) use ($transaction){
                                                        $query->where("id",$transaction->id);                                                                                                                
                                                        $query->where("type","production_sell");                                                                                                                
                                                    })->where("type","debit")
                                                    ->first();
                    if(!empty($mfg)){
                        $account_transaction = $mfg->account_id;
                        $action_date         = $mfg->operation_date;
                        $mfg->delete();
                        $old_account         = Account::find($account_transaction); 
                        if($old_account->cost_center!=1){  self::nextRecords($old_account->id,$old_account->business_id,$action_date); }
                    }
                    AccountTransaction::mfg_account($transaction,$account_production,$total_po);
                }
            }else{
                AccountTransaction::mfg_account($transaction,$account_production,$total_po);
            }
            
            # profit
            if($old_profit!=null){
                if($old_profit == $account_profit){
                    AccountTransaction::mfg_profit($transaction,$account_profit,$Cost_extra,$total_po);
                }else{
                    $profit =   AccountTransaction::where("account_id",$old_profit)
                                                    ->whereHas("transaction",function($query) use ($transaction){
                                                        $query->where("id",$transaction->id);                                                                                                                
                                                        $query->where("type","production_sell");                                                                                                                
                                                    })->where("type","credit")
                                                    ->first();
                    if(!empty($profit)){
                        $account_transaction = $profit->account_id;
                        $action_date         = $profit->operation_date; 
                        $profit->delete();
                        $old_account         = Account::find($account_transaction); 
                        if($old_account->cost_center!=1){  self::nextRecords($old_account->id,$old_account->business_id,$action_date); }
                    }
                    AccountTransaction::mfg_profit($transaction,$account_profit,$Cost_extra,$total_po);
                }
            }else{
                AccountTransaction::mfg_profit($transaction,$account_profit,$Cost_extra,$total_po);
            }


            # purchase account
            $purchase =   AccountTransaction::where("account_id",$purchase_id)
                                                ->whereHas("transaction",function($query) use ($transaction){
                                                    $query->where("id",$transaction->id);                                                                                                                
                                                    $query->where("type","production_sell");                                                                                                                
                                                })->where("type","credit")
                                                ->first();
            
            if(empty($purchase)){
                // purhase..............
                AccountTransaction::add_main_id($purchase_id,round($total_po,2),'credit',$transaction,'Add Stock');
                $type="Production";
            
                $item    = Transaction::find($transaction->mfg_parent_production_purchase_id);
                \App\Models\Entry::create_entries($item,$type);
                $entry   = \App\Models\Entry::where("account_transaction",$item->id)->where("state","Production")->first();
                ($entry) ? $entry->id :null; 
                \App\AccountTransaction::where("transaction_id",$transaction->id)->whereIn("note",["Add Stock"])->update([
                                                    "entry_id" => ($entry) ? $entry->id :null
                                                ]);
            }else{
                $purchase->type      = "credit";
                $purchase->amount    = round($total_po,2);
                $purchase->note      = 'Add Stock';
                $purchase->update();
                $item    = Transaction::find($transaction->mfg_parent_production_purchase_id);
                $entry   = \App\Models\Entry::where("account_transaction",$item->id)->where("state","Production")->first();
                ($entry) ? $entry->id :null; 
                \App\AccountTransaction::where("transaction_id",$transaction->id)->whereIn("note",["Add Stock"])->update([
                                                    "entry_id" => ($entry) ? $entry->id :null
                                                ]);
            }
            
            
        }
        // ............................................................................
        public static function mfg_account($transaction,$account_production,$total_po)
        {
            $mfg =   AccountTransaction::where("account_id",$account_production)
                                        ->whereHas("transaction",function($query) use ($transaction){
                                            $query->where("id",$transaction->id);                                                                                                                
                                            $query->where("type","production_sell");                                                                                                                
                                        })->where("type","debit")
                                        ->first();   
            
            if(!empty($transaction)){

                $total_ss =  round($transaction->final_total,2);

                if(empty($mfg)){
                    $name     = \App\Account::find($account_production)->name;
                    AccountTransaction::add_main($name,round($total_ss,2),"debit",$transaction,'Add Stock');
                }else{
                    $mfg->type      = "debit";
                    $mfg->amount    = round($total_ss,2);
                    $mfg->note      = 'Add Stock';
                    $mfg->update();
                    $old_account      = Account::find($mfg->account_id); 
                    if($old_account->cost_center!=1){  self::nextRecords($old_account->id,$old_account->business_id,$mfg->operation_date); }
                }
            }
        }
        // .............................................................................
        public static function mfg_profit($transaction,$account_profit,$Cost_extra,$total_po)
        {
            //...  profit account
            $profit =   AccountTransaction::where("account_id",$account_profit)
                                        ->whereHas("transaction",function($query) use ($transaction){
                                            $query->where("id",$transaction->id);                                                                                                                
                                            $query->where("type","production_sell");                                                                                                                
                                        })->where("type","credit")
                                        ->first();
            if($Cost_extra!=null){
                $total_pr =  ($total_po*$Cost_extra)/100;
                if(empty($profit)){
                    $name     = \App\Account::find($account_profit)->name;
                    AccountTransaction::add_main($name,round($total_pr,2),"credit",$transaction,'Add Stock');
                }else{
                    $profit->type      = "credit";
                    $profit->amount    = round($total_pr,2);
                    $profit->note      = 'Add Stock';
                    $profit->update();
                    $old_account      = Account::find($profit->account_id); 
                    if($old_account->cost_center!=1){  self::nextRecords($old_account->id,$old_account->business_id,$profit->operation_date); }
                }
            }
        }
    //  ************************************************ \\

    //  **7********* ADDITIONAL TRANSACTION *********7** \\
        public static function delete_previous_transaction($id,$check)
        {
            \App\Models\Entry::where("account_transaction",$id)->delete();
            $shipping_id = \App\Models\AdditionalShipping::where("transaction_id",$id)->first();
            if(!empty($shipping_id)){
            
                \App\Models\Entry::where("shipping_id",$shipping_id->id)->delete();
            }
            \App\Models\StatusLive::where("transaction_id",$id)->whereNotNull("shipping_item_id")->delete();
            \App\Models\StatusLive::where("transaction_id",$id)->where("num_serial","!=",1)->delete();
        
            \App\AccountTransaction::where('transaction_id',$id)->whereHas("additional_shipping_item",function($query) use($check){
                                                    $query->whereHas("additional_shipping",function($query) use($check){
                                                        $query->where("type",$check);
                                                    });
                                        })->whereNotNull('additional_shipping_item_id')->delete();
        }
        // .....................................................................
        public static function last_entry($id)
        {
            if($id != null){
                $entry_with_transaction = AccountTransaction::where("entry_id",$id)
                                                        ->whereHas('account',function($query){
                                                            $query->where('cost_center',0);
                                                        })
                                                        ->orderBy("id","asc")
                                                        ->first();
                return $entry_with_transaction->id;
            }else{
                return null ;
            }
        }
    //  ************************************************ \\

    // **8********* OLD MOVEMENT TRANSACTION ********8** \\
        ## refresh by id  all rows
        public static function oldBalance($id,$business_id,$date){
            $balance     = 0; 
            $list_id     = \App\AccountTransaction::whereHas("account",function($query) use($id,$business_id){ 
                                                        $query->where("id",$id); 
                                                        $query->where('business_id', $business_id);
                                                        $query->where("cost_center","=",0);
                                                    })
                                                    ->whereNull("deleted_at") 
                                                    ->whereNull('for_repeat')  
                                                    ->where("amount",">",0) 
                                                    ->orderBy('operation_date', 'asc')
                                                    ->orderBy('id', 'asc')
                                                    ->groupBy('id')
                                                    ->select([
                                                        'id',
                                                        'account_id',
                                                        'for_repeat',
                                                        'amount',
                                                        'operation_date',
                                                        'deleted_at',
                                                        'current_balance',
                                                        'balance_type' 
                                                        ])
                                                    // ->pluck('id');
                                                    ->get();
             
            
            $container = [] ; $first = 0; $total_balance = 0;
            foreach($list_id as $key => $one){ 
                $container[] = $one->id;
                if($first == 0){
                    $first = 1;
                    $transaction = \App\AccountTransaction::whereNull("deleted_at")
                                                    ->whereHas("account",function($query) use($id,$business_id){ 
                                                        $query->where("id",$id); 
                                                        $query->where('business_id', $business_id);
                                                        $query->where("cost_center","=",0);
                                                    })
                                                    ->select(
                                                    \DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount  * -1 , AT.amount)) from account_transactions as AT WHERE 
                                                             AT.operation_date <=  \''.$one->operation_date.'\' AND AT.account_id  = account_transactions.account_id AND AT.for_repeat IS NULL AND AT.deleted_at IS NULL AND AT.id
                                                             <  \''.$one->id.'\' AND AT.id NOT IN ('. implode(',',$container).') ) as balance'),
                                                    \DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount * -1 ,  AT.amount)) from account_transactions as AT WHERE 
                                                            AT.operation_date <  \''.$one->operation_date.'\'   AND AT.account_id  = account_transactions.account_id AND AT.for_repeat IS NULL AND AT.deleted_at IS NULL AND AT.id
                                                            > \''.$one->id.'\' AND AT.id NOT IN ('.implode(',',$container).') ) as balance_more'),
                                                    \DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount * -1,  AT.amount)) from account_transactions as AT WHERE 
                                                            AT.operation_date =  \''.$one->operation_date.'\'  AND AT.account_id  = account_transactions.account_id AND AT.for_repeat IS NULL AND AT.deleted_at IS NULL AND AT.id
                                                            = \''.$one->id.'\' ) as balance_more_id'))
                                                    ->whereNull('for_repeat')  
                                                    ->where("amount",">",0) 
                                                    ->orderBy('operation_date', 'asc')
                                                    ->orderBy('id', 'asc')
                                                    // ->groupBy('id')
                                                    ->first();
                    if($id == 321){
                        // $last_balance = self::lastBalance($list_id[2]->account_id,$business_id,$list_id[2]->operation_date);
                        // $listed       = self::nextRecords($list_id[1]->account_id,$business_id,$list_id[1]->operation_date);
                        // dd($transaction);
                    } 
                    $type   = "";
                    if($transaction != null){
                        $main          =  $transaction->balance ;
                        $second        =  $transaction->balance_more ;
                        $third         =  $transaction->balance_more_id  ;
                        $epsilon       =  0.000001;
                        $balance       = ($main < 0)?($balance - abs($main)):($balance + abs($main)) ;
                        $balance       = ($second < 0)?($balance - abs($second)):($balance + abs($second)) ;
                        $balance       = ($third < 0)?($balance - abs($third)):($balance + abs($third)) ;
                        $balance       = ( abs($balance) < $epsilon )?0:$balance;
                        $type          = ($balance<0)?"credit":(($balance == 0)?"":"debit");
                        $total_balance = $balance;
                    }
                }else{
                    $type = "";
                    $transaction = \App\AccountTransaction::find($one->id);
                    if($transaction != null){
                        $main           =  $transaction->amount ;
                        $epsilon        =  0.000001;
                        $total_balance  = ($transaction->type == 'credit')?($total_balance - abs($main)):($total_balance + abs($main)) ;
                        $type           = ($total_balance<0)?"credit":(($total_balance == 0)?"":"debit");
                        $total_balance  = ( abs($total_balance) < $epsilon )?0:$total_balance;
                    }
                } 
                $one->current_balance = $total_balance;
                $one->balance_type    = $type;
                $one->update(); 
            } 
            $acc          = \App\Account::find($id);
            $acc->balance = $total_balance;
            $acc->update();
            return "";
        }
        ## get the last balance by before date
        public static function lastBalance($id,$business_id,$date) {
            // ..................................................1..
            $yesterday   = \Carbon::createFromFormat('Y-m-d H:i:s', $date)->subDay()->format('Y-m-d');
            $transaction = \App\AccountTransaction::whereHas("account",function($query) use($id,$business_id){ 
                                                        $query->where("id",$id); 
                                                        $query->where('business_id', $business_id);
                                                        $query->where("cost_center","=",0);
                                                    })
                                                    ->whereNull("deleted_at") 
                                                    ->whereNull('for_repeat')  
                                                    ->where("amount",">",0)
                                                    ->whereDate("operation_date","<=",$date)
                                                    ->orderBy('operation_date', 'desc')
                                                    ->orderBy('id', 'desc')
                                                    ->groupBy('id')
                                                    ->select('id','account_id','for_repeat','amount','operation_date','deleted_at','current_balance','balance_type') 
                                                    // ->pluck('id');
                                                    ->first();
            // .....................................................*..
            return ($transaction)?$transaction->current_balance:0;
        }
        ## get the last balance by last one
        public static function lastOne($row) {
            // ..................................................1.. 
            $last        = \App\AccountTransaction::whereHas("account",function($query) use($row){ 
                                    $query->where("id",$row->id); 
                                    $query->where("id",$row->account_id); 
                                    $query->where("cost_center","=",0);
                                })
                                ->whereNull("deleted_at") 
                                ->whereNull('for_repeat')  
                                ->where("amount",">",0) 
                                ->orderBy('operation_date', 'desc')
                                ->orderBy('id', 'desc')
                                ->groupBy('id')
                                ->select([
                                    'id',
                                    'account_id',
                                    'for_repeat',
                                    'amount',
                                    'operation_date',
                                    'deleted_at',
                                    'current_balance',
                                    'balance_type' 
                                    ])->first();
          
            $blc_account = ($last)?$last->current_balance:0;
            // .....................................................*..
            return $blc_account;
        }
        ## refresh all next records
        public static function nextRecords($id,$business_id,$date) {
            
            /**
             * # select from account transaction table all records that not equal to zero . 
             * # less than the action date .
             * # and filter of repeated rows because two rows relation by payment vouchers .
             * # reverse ordered by date and id
             * # here show the new record and last one before action date.
             * # get usually second row and get ( current_balance ) value from it .    
             * 
             */
            $first         = \App\AccountTransaction::whereHas("account",function($query) use($id,$business_id){ 
                                                                $query->where("id",$id); 
                                                                $query->where('business_id', $business_id);
                                                                $query->where("cost_center","=",0);
                                                            })
                                                            ->where("amount",">",0) 
                                                            ->whereNull("deleted_at") 
                                                            ->whereNull('for_repeat')  
                                                            ->orderBy('operation_date','asc')
                                                            ->orderBy('id','asc') 
                                                            ->select([
                                                                'id',
                                                                'account_id',
                                                                'type',
                                                                'for_repeat',
                                                                'amount',
                                                                'operation_date',
                                                                'deleted_at',
                                                                'current_balance',
                                                                'balance_type' 
                                                            ]) 
                                                            ->first();
            $balance_account = 0; 
            $before_one_day  = (strlen($date)<19)?(\Carbon::createFromFormat('Y-m-d',$date)->subDay()->format('Y-m-d')):(\Carbon::createFromFormat('Y-m-d H:i:s',$date)->subDay()->format('Y-m-d H:i:s')); 
            $before_id       = \App\AccountTransaction::whereHas("account",function($query) use($id,$business_id){ 
                                                                $query->where("id",$id); 
                                                                $query->where('business_id', $business_id);
                                                                $query->where("cost_center","=",0);
                                                            })
                                                            ->where("amount",">",0) 
                                                            ->where("operation_date","<=",$before_one_day) 
                                                            ->whereNull("deleted_at") 
                                                            ->whereNull('for_repeat')  
                                                            ->orderBy('operation_date','desc')
                                                            ->orderBy('id','desc') 
                                                            ->select([
                                                                'id',
                                                                'account_id',
                                                                'type',
                                                                'for_repeat',
                                                                'amount',
                                                                'operation_date',
                                                                'deleted_at',
                                                                'current_balance',
                                                                'balance_type' 
                                                            ]) 
                                                            ->limit(2)
                                                            ->get();
            $current_balance = 0;
            
            if(count($before_id)>0){
                if(count($before_id)==1){
                    foreach($before_id as $ii){
                        $current_balance       =  ($ii->type == "credit")?$ii->amount*-1:$ii->amount;
                        $ii->current_balance   =  $current_balance ;
                        $ii->balance_type      =  ($current_balance<0)?"credit":"debit" ;
                        $before_one_day        =  $ii->operation_date; 
                        $ii->update();
                    }
                }else{
                    $line  = null;
                    foreach($before_id as $key => $ii){
                        if($key == 0){$line = $ii;}
                        if($ii->id == $first->id){
                            $current_balance = ($ii->type == "credit")?$ii->amount*-1:$ii->amount;
                            $ii->current_balance = $current_balance;
                            $ii->update();
                        }else{
                            $current_balance = $ii->current_balance ;
                        }
                    }
                    if($line!=null){
                        $current_amount        =  ($line->type == "credit")?$line->amount*-1:$line->amount;
                        $line->current_balance =  $current_balance + $current_amount ;
                        $line->balance_type    =  (($current_balance + $current_amount)<0)?"credit":"debit" ;
                        $before_one_day        =  $line->operation_date;
                        $line->update();
                        $current_balance = $current_balance + $current_amount;
                    }
                }
                $balance_account = $current_balance; 
            }else{
                $before_id       = \App\AccountTransaction::whereHas("account",function($query) use($id,$business_id){ 
                                                                $query->where("id",$id); 
                                                                $query->where('business_id', $business_id);
                                                                $query->where("cost_center","=",0);
                                                            })
                                                            ->where("amount",">",0) 
                                                            ->where("operation_date","<=",$date) 
                                                            ->whereNull("deleted_at") 
                                                            ->whereNull('for_repeat')  
                                                            ->orderBy('operation_date', 'desc')
                                                            ->orderBy('id', 'desc') 
                                                            ->select([
                                                                'id',
                                                                'account_id',
                                                                'type',
                                                                'for_repeat',
                                                                'amount',
                                                                'operation_date',
                                                                'deleted_at',
                                                                'current_balance',
                                                                'balance_type' 
                                                            ]) 
                                                            ->limit(2)
                                                            ->get();
                if(count($before_id)==1){
                    foreach($before_id as $ii){
                        $current_balance       =  ($ii->type == "credit")?$ii->amount*-1:$ii->amount;
                        $ii->current_balance   =  $current_balance ;
                        $ii->balance_type      =  ($current_balance<0)?"credit":"debit" ;
                        $before_one_day        =  $ii->operation_date; 
                        $ii->update(); 
                    }
                }else{
                    if(count($before_id)!=0){
                        $line  = null;
                        foreach($before_id as $key => $ii){
                            if($key == 0){$line = $ii;}
                            if($ii->id == $first->id){
                                $current_balance = ($ii->type == "credit")?$ii->amount*-1:$ii->amount;
                                $ii->current_balance = $current_balance;
                                $ii->update();
                            }else{
                                $current_balance = $ii->current_balance ;
                            }
                        }
                        if($line!=null){
                            $current_amount        =  ($line->type == "credit")?$line->amount*-1:$line->amount;
                            $line->current_balance =  $current_balance + $current_amount ;
                            $line->balance_type    =  (($current_balance + $current_amount)<0)?"credit":"debit" ;
                            $before_one_day        =  $line->operation_date;
                            $line->update();
                            $current_balance = $current_balance + $current_amount ;
                        }
                    }
                }
                $balance_account = $current_balance; 
            }
            $balance_current_first  = 0; 
            // .........................................................................refresh #..
            $list_after_id   = \App\AccountTransaction::whereHas("account",function($query) use($id,$business_id){ 
                                                                $query->where("id",$id); 
                                                                $query->where('business_id', $business_id);
                                                                $query->where("cost_center","=",0);
                                                            })
                                                            ->where("amount",">",0) 
                                                            ->where("operation_date",">=",$before_one_day) 
                                                            ->whereNull("deleted_at") 
                                                            ->whereNull('for_repeat')  
                                                            ->orderBy('operation_date', 'asc')
                                                            ->orderBy('id', 'asc') 
                                                            ->select([
                                                                'id',
                                                                'account_id',
                                                                'for_repeat',
                                                                'amount',
                                                                'type',
                                                                'operation_date',
                                                                'deleted_at',
                                                                'current_balance',
                                                                'balance_type' 
                                                            ]) 
                                                            ->get();
            // if($id == 28){
            //     dd($before_one_day ,$list_after_id,$before_id,$balance_account,$current_balance,$first);
            // }
            if(count($list_after_id)>0){
                foreach($list_after_id as $key => $one){
                    $main                 = ($one->type == "credit")?$one->amount*-1:$one->amount ;
                    $epsilon              = 0.000001;
                    if($key == 0){
                        $balance_current_first    = (count($before_id)==0)?$main:$one->current_balance;
                        $type                     = ($balance_current_first<0)?"credit":(($balance_current_first == 0)?"":"debit");
                        if(count($before_id)==0){
                            $one->current_balance = $balance_current_first;
                            $one->balance_type    = $type;
                            $one->update(); 
                        }
                    }else{
                        $balance                = ($main < 0)?(-1*abs($main)):(abs($main)) ;
                        $balance_current_first += $balance;
                        $type                   = ($balance_current_first<0)?"credit":(($balance_current_first == 0)?"":"debit");
                        $one->current_balance   = $balance_current_first;
                        $one->balance_type      = $type;
                        $one->update(); 
                    }  
                }  
                $balance_account = $balance_current_first;
            }  
            $acc             = \App\Account::find($id);
            $acc->balance    = $balance_account;
            $acc->update();
            return true;

        }
    // ************************************************* \\

    //  **1** ********* RELATION SECTION  *********** \\
        public function payment_voucher()
        {
            return $this->belongsTo('App\Models\PaymentVoucher','payment_voucher_id');
        }
        // **2** 
        public function payment()
        {
            return $this->belongsTo('App\TransactionPayment','transaction_payment_id');
        }
        // **3** 
        public function daily_payment_item()
        {
            return $this->belongsTo('App\Models\DailyPaymentItem','daily_payment_item_id');
        }
        // **4** 
        public function additional_shipping_item()
        {
            return $this->belongsTo('App\Models\AdditionalShippingItem','additional_shipping_item_id');
        }
        // **5** 
        public function gournal_voucher_item()
        {
            return $this->belongsTo('App\Models\GournalVoucherItem','gournal_voucher_item_id');
        }
        // **6** 
        public function gournal_voucher()
        {
            return $this->belongsTo('App\Models\GournalVoucher','gournal_voucher_id');
        }
        // **7** 
        public function cheque()
        {
            return $this->belongsTo('App\Models\Check','check_id');
        }
        // **8** 
        public function media()
        {
            return $this->morphMany(\App\Media::class, 'model');
        } 
        // **9** 
        public function check()
        {
            return $this->belongsTo("\App\Models\Check","check_id");
        }
        // **10** 
        public function  entry()
        {
            return $this->belongsTo("\App\Models\Entry","entry_id");
        }
        // **11** 
        public function transaction()
        {
            return $this->belongsTo(\App\Transaction::class, 'transaction_id');
        }
        // **12** 
        public function return_transaction()
        {
            return $this->belongsTo(\App\Transaction::class, 'return_transaction_id');
        }
        // **13** 
        public function transfer_transaction()
        {
            return $this->belongsTo(\App\AccountTransaction::class, 'transfer_transaction_id');
        }
        // **14** 
        public function account()
        {
            return $this->belongsTo(\App\Account::class, 'account_id');
        }
        // **15** 
        public function entries()
        {
            return $this->belongsTo('App\Models\Entry','entry_id');
        }
        // **16**
        // .. Osama .. PARENT REFERENCE
        public function getParentRefAttribute()
        {
            if ($this->payment_voucher) {
                return $this->payment_voucher->ref_no;
            }elseif ($this->daily_payment_item) {
                return $this->daily_payment_item->daily_payment->ref_no;
            }elseif ($this->gournal_voucher_item) {
                return $this->gournal_voucher_item->gournal_voucher->ref_no;
            }elseif ($this->cheque) {
                return $this->cheque->ref_no;
            }elseif ($this->gournal_voucher) {
                return $this->gournal_voucher->ref_no;
            }
        }
    //  ********************************************* \\ 
}
