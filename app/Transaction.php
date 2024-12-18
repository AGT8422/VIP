<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    //Transaction types = ['purchase','sell','expense','stock_adjustment','sell_transfer','purchase_transfer','opening_stock','sell_return','opening_balance','purchase_return', 'payroll', 'expense_refund']

    //Transaction status = ['received','pending','ordered','draft','final', 'in_transit', 'completed']

    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table   = 'transactions';
    protected $appends = ['recieved_margin','delivered_margin'];
    
    

    public static function shouldDelivered($row)
    {
         
        $transaction = Transaction::get();
        $total       = 0 ;
        foreach($transaction as $it){
            
            $TransactionSellLine = \App\TransactionSellLine::where("transaction_id",$it->id)->where("product_id",$row->product_id)->select(\DB::raw("SUM(quantity) as total"))->first()->total;
            $DeliveredPrevious   = \App\Models\DeliveredPrevious::where("transaction_id",$it->id)->where("product_id",$row->product_id)->select(\DB::raw("SUM(current_qty) as total"))->first()->total;
            $wrong               = \App\Models\DeliveredWrong::where("transaction_id",$it->id)->where("product_id",$row->product_id)->select(\DB::raw("SUM(current_qty) as total"))->first()->total;
            
            if($DeliveredPrevious == null){
                $total += $TransactionSellLine ;
            }else if($TransactionSellLine <= $DeliveredPrevious){
                $total += $TransactionSellLine -  $DeliveredPrevious;
            } else if( $DeliveredPrevious < $TransactionSellLine ){
                $total += $TransactionSellLine -  $DeliveredPrevious;
            }
        }
        
        return $total;
    }
    public function purchase_lines()
    {
        return $this->hasMany(\App\PurchaseLine::class );
    }
    public function cost_center()
    {
        return $this->belongsTo(\App\Account::class,'cost_center_id');
    }
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class,'return_parent_id');
    }
    public function showBill($user) {
        $sales    = \App\Transaction::whereIn("type",["sale","sell_return"])->where("created_by",$user->id)->select(["id","final_total","contact_id","store","discount_type","discount_amount","status","agent_id","total_before_tax","type","transaction_date","invoice_no","payment_status","pattern_id","sub_status","created_by"])->with(["sell_lines.product"])->get();
        
        $array_d         = [] ;
        $payment_bill    = [];
        $payment_bill_id = [];
        $voucher_bill_id = [];
        $check_bill_id   = [];
        $accountTransaction_bill_id   = [];
        foreach($sales as $it){
            $payment    = \App\TransactionPayment::join("transactions as tr","tr.id","transaction_payments.transaction_id")->where("transaction_id",$it->id)->whereOr("payment_voucher_id","!=",null)->select("transaction_payments.id","transaction_payments.amount","transaction_payments.method","transaction_payments.payment_voucher_id","transaction_payments.check_id","transaction_payments.created_by","transaction_payments.paid_on","tr.type")->where("transaction_payments.created_by",$user->id)->get();
            foreach($payment as $i){
                $setting = \App\Models\SystemAccount::select("*")->first(); 
                
                $ttype        = ($it->type != "sale")?'credit':'debit'  ;
                $check_type   = ($it->type != "sale")?'debit':'credit'  ;
                $cash         = \App\AccountTransaction::where("payment_voucher_id",$i->payment_voucher_id)->where("type",$ttype)->where("account_id",$user->user_account_id)->first();
                $visa         = \App\AccountTransaction::where("payment_voucher_id",$i->payment_voucher_id)->where("type",$ttype)->where("account_id",$user->user_visa_account_id)->first();
                $cheque       = \App\AccountTransaction::where("check_id",$i->check_id)->where("type",$check_type)->where("account_id",$setting->cheque_collection)->first();
                 
                if(!empty($cheque)){
                        $cheque_amount      = $cheque->amount;
                        $accountTransaction_bill_id[] = $i->id; 
                        $check_bill_id[]   = $i->check_id;
                        if(!in_array($i->check_id,$check_bill_id) && $i->check_id != null){
                            $check_bill_id[] = $i->check_id;
                        }
                }else{
                        $cheque_amount = 0;
                    
                }
                if(!empty($visa)){
                        $visa_amount       = $visa->amount;
                        $accountTransaction_bill_id[] =$i->id;
                        if(!in_array($i->payment_voucher_id,$voucher_bill_id)  && $i->payment_voucher_id != null){
                            $voucher_bill_id[] = $i->payment_voucher_id;
                        }
                }else{
                        $visa_amount = 0;
                    
                }
                if(!empty($cash)){
                        $cash_amount       = $cash->amount ;
                        $accountTransaction_bill_id[] =$i->id; 
                       if(!in_array($i->payment_voucher_id,$voucher_bill_id)  && $i->payment_voucher_id != null) {
                            $voucher_bill_id[] = $i->payment_voucher_id;
                        }
                }else{
                        $cash_amount = 0 ;
                    
                }
                
                $i->cash_amount   = $cash_amount;
                $i->visa_amount   = $visa_amount;
                $i->cheque_amount = $cheque_amount;
            }
            
            if(!in_array($it->id,$payment_bill_id)){
                if(count($payment)>0){
                    $payment_bill[$it->id] = json_decode($payment);
                    $payment_bill_id[]     = $it->id;
                }
            }
        }
        
        if(count($voucher_bill_id)>0){
                $paymentGlobalCash   = \App\AccountTransaction::whereNotIn("payment_voucher_id",$voucher_bill_id)->where("created_by",$user->id)->where("type","debit")->where("account_id",$user->user_account_id)->get();
                
                $paymentGlobalVisa   = \App\AccountTransaction::whereNotIn("payment_voucher_id",$voucher_bill_id)->where("created_by",$user->id)->where("type","debit")->where("account_id",$user->user_visa_account_id)->get();
       
                $array  = [] ;
                $arrays = [] ;
                foreach($paymentGlobalCash as $i){
                            $array[]  = [
                                "id"     => $i->payment_voucher_id,    
                                "amount" => $i->amount,    
                                "date"   => $i->operation_date,    
                            ];
                            $accountTransaction_bill_id[] =$i->id; 
                }
                foreach($paymentGlobalVisa as $s){
                    $arrays[]  = [
                        "id"     => $s->payment_voucher_id,    
                        "amount" => $s->amount,    
                        "date"   => $s->operation_date,    
                    ];
                    $accountTransaction_bill_id[] =$i->id; 
                }
                $data["paymentGlobalCash"] = $array;
                $data["paymentGlobalVisa"] = $arrays;
            } 
            
             
        $setting = \App\Models\SystemAccount::first();
        $paymentGlobalCheque = \App\AccountTransaction::whereNull("transaction_payment_id")->whereNull("transaction_id")->whereNotNull("check_id")->where("created_by",$user->id)->where("type",$check_type)->where("account_id",$setting->cheque_collection)->get();
        foreach($paymentGlobalCheque as $s){
            $array_d[]  = [
                "id"     => $s->check_id,    
                "amount" => $s->amount,    
                "date"   => $s->operation_date,    
            ];
        }
        
        $data["paymentGlobalCheque"]     = $array_d;
        
        $data["sales"]                   = $sales;
        $data["payment_bill"]            = $payment_bill ;
        return $data;
    }
    public function recived_previous()
    {
        return $this->hasMany(\App\Models\RecievedPrevious::class,'transaction_id');
    }
    public function delivered_previous()
    {
        return $this->hasMany(\App\Models\DeliveredPrevious::class,'transaction_id');
    }
    public function getRecievedMarginAttribute()
    {
        return ($this->purchase_lines->sum('quantity') - $this->recived_previous->sum('current_qty'));
    }
    public function additional_shipings()
    {
        return $this->hasMany('\App\Models\AdditionalShipping','transaction_id');
    }
    public function entries()
    {
        return $this->hasMany('\App\Models\Entry','account_transaction');
    }
    public function sell_lines()
    {
        //  dd($this->hasMany(\App\TransactionSellLine::class ));
        return $this->hasMany(\App\TransactionSellLine::class);
    }
    public function getDeliveredMarginAttribute()
    {
        return ($this->sell_lines->sum('quantity') - $this->delivered_previous->sum('current_qty'));
    }
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }
    public static function agent($id,$business_id)
    {
        $users     = "";
        $us        = User::find($id);
        if(!empty($us)){
            $users = $us->first_name;
        }
        return  $users;
    }

    public function payment_lines()
    {
        return $this->hasMany(\App\TransactionPayment::class, 'transaction_id');
    }
    public function payment_lines_id()
    {
        return $this->belongsTo(\App\TransactionPayment::class, 'id');
    }

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }
    public function pattern()
    {
        return $this->belongsTo(\App\Models\Pattern::class, 'pattern_id');
    }
    public function warehouse_to()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'transfer_parent_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'store');
    }
  
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    public function tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax_id');
    }

    public function stock_adjustment_lines()
    {
        return $this->hasMany(\App\StockAdjustmentLine::class);
    }

    public function sales_person()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function return_parent()
    {
        return $this->hasOne(\App\Transaction::class, 'return_parent_id');
    }

    public function return_parent_sell()
    {
        return $this->belongsTo(\App\Transaction::class, 'return_parent_id');
    }

    public function table()
    {
        return $this->belongsTo(\App\Restaurant\ResTable::class, 'res_table_id');
    }

    public function service_staff()
    {
        return $this->belongsTo(\App\User::class, 'res_waiter_id');
    }

    public function recurring_invoices()
    {
        return $this->hasMany(\App\Transaction::class, 'recur_parent_id');
    }

    public function recurring_parent()
    {
        return $this->hasOne(\App\Transaction::class, 'id', 'recur_parent_id');
    }

    public function price_group()
    {
        return $this->belongsTo(\App\SellingPriceGroup::class, 'selling_price_group_id');
    }

    public function types_of_service()
    {
        return $this->belongsTo(\App\TypesOfService::class, 'types_of_service_id');
    }

    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
    
    /**
     * Retrieves documents path if exists
     */
    public function getDocumentPathAttribute()
    {
        $path = !empty($this->document) ? asset('public/uploads/documents/' . $this->document) : null;
        
        return $path;
    }
    public function account_transactions()
    {
        return $this->hasMany('\App\AccountTransaction','transaction_id');
    }

    /**
     * Removes timestamp from document name
     */
    public function getDocumentNameAttribute()
    {
        $document_name = !empty(explode("_", $this->document, 2)[1]) ? explode("_", $this->document, 2)[1] : $this->document ;
        return $document_name;
    }

    public function subscription_invoices()
    {
        return $this->hasMany(\App\Transaction::class, 'recur_parent_id');
    }

    /**
     * Shipping address custom method
     */
    public function shipping_address($array = false)
    {
        $addresses = !empty($this->order_addresses) ? json_decode($this->order_addresses, true) : [];

        $shipping_address = [];

        if (!empty($addresses['shipping_address'])) {
            if (!empty($addresses['shipping_address']['shipping_name'])) {
                $shipping_address['name'] = $addresses['shipping_address']['shipping_name'];
            }
            if (!empty($addresses['shipping_address']['company'])) {
                $shipping_address['company'] = $addresses['shipping_address']['company'];
            }
            if (!empty($addresses['shipping_address']['shipping_address_line_1'])) {
                $shipping_address['address_line_1'] = $addresses['shipping_address']['shipping_address_line_1'];
            }
            if (!empty($addresses['shipping_address']['shipping_address_line_2'])) {
                $shipping_address['address_line_2'] = $addresses['shipping_address']['shipping_address_line_2'];
            }
            if (!empty($addresses['shipping_address']['shipping_city'])) {
                $shipping_address['city'] = $addresses['shipping_address']['shipping_city'];
            }
            if (!empty($addresses['shipping_address']['shipping_state'])) {
                $shipping_address['state'] = $addresses['shipping_address']['shipping_state'];
            }
            if (!empty($addresses['shipping_address']['shipping_country'])) {
                $shipping_address['country'] = $addresses['shipping_address']['shipping_country'];
            }
            if (!empty($addresses['shipping_address']['shipping_zip_code'])) {
                $shipping_address['zipcode'] = $addresses['shipping_address']['shipping_zip_code'];
            }
        }

        if ($array) {
            return $shipping_address;
        } else {
            return implode(', ', $shipping_address);
        }
    }

    /**
     * billing address custom method
     */
    public function billing_address($array = false)
    {
        $addresses = !empty($this->order_addresses) ? json_decode($this->order_addresses, true) : [];

        $billing_address = [];

        if (!empty($addresses['billing_address'])) {
            if (!empty($addresses['billing_address']['billing_name'])) {
                $billing_address['name'] = $addresses['billing_address']['billing_name'];
            }
            if (!empty($addresses['billing_address']['company'])) {
                $billing_address['company'] = $addresses['billing_address']['company'];
            }
            if (!empty($addresses['billing_address']['billing_address_line_1'])) {
                $billing_address['address_line_1'] = $addresses['billing_address']['billing_address_line_1'];
            }
            if (!empty($addresses['billing_address']['billing_address_line_2'])) {
                $billing_address['address_line_2'] = $addresses['billing_address']['billing_address_line_2'];
            }
            if (!empty($addresses['billing_address']['billing_city'])) {
                $billing_address['city'] = $addresses['billing_address']['billing_city'];
            }
            if (!empty($addresses['billing_address']['billing_state'])) {
                $billing_address['state'] = $addresses['billing_address']['billing_state'];
            }
            if (!empty($addresses['billing_address']['billing_country'])) {
                $billing_address['country'] = $addresses['billing_address']['billing_country'];
            }
            if (!empty($addresses['billing_address']['billing_zip_code'])) {
                $billing_address['zipcode'] = $addresses['billing_address']['billing_zip_code'];
            }
        }

        if ($array) {
            return $billing_address;
        } else {
            return implode(', ', $billing_address);
        }
    }

    public function cash_register_payments()
    {
        return $this->hasMany(\App\CashRegisterTransaction::class);
    }

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function transaction_for()
    {
        return $this->belongsTo(\App\User::class, 'expense_for');
    }

    /**
     * Returns the list of discount types.
     */
    public static function discountTypes()
    {
        return [
                'fixed' => __('lang_v1.fixed'),
                'percentage' => __('lang_v1.percentage')
            ];
    }

    public static function transactionTypes()
    {
        return  [
                'sale' => __('sale.sale'),
                'purchase' => __('lang_v1.purchase'),
                'sell_return' => __('lang_v1.sell_return'),
                'purchase_return' =>  __('lang_v1.purchase_return'),
                'opening_balance' => __('lang_v1.opening_balance'),
                'payment' => __('lang_v1.payment')
            ];
    }

    public static function getPaymentStatus($transaction)
    {
        $payment_status = $transaction->payment_status;
        
        if (in_array($payment_status, ['partial', 'due']) && !empty($transaction->pay_term_number) && !empty($transaction->pay_term_type)) {
            $transaction_date = \Carbon::parse($transaction->transaction_date);
            $due_date = $transaction->pay_term_type == 'days' ? $transaction_date->addDays($transaction->pay_term_number) : $transaction_date->addMonths($transaction->pay_term_number);
            $now = \Carbon::now();
            if ($now->gt($due_date)) {
                $payment_status = $payment_status == 'due' ? 'overdue' : 'partial-overdue';
            }
        }

        return $payment_status;
    }

    /**
     * Due date custom attribute
     */
    public function getDueDateAttribute()
    {
        $due_date = null;
        if (!empty($this->pay_term_type) && !empty($this->pay_term_number)) {
            $transaction_date = \Carbon::parse($this->transaction_date);
            $due_date = $this->pay_term_type == 'days' ? $transaction_date->addDays($this->pay_term_number) : $transaction_date->addMonths($this->pay_term_number);
        }

        return $due_date;
    }

    public static function getSellStatuses()
    {
        return ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' => __('lang_v1.quotation'), 'proforma' => __('lang_v1.proforma')];
    }

    /**
     * Attributes to be logged for activity
     */
    public function getLogPropertiesAttribute() {
        $properties = [];

        if (in_array($this->type, ['Stock_Out'])) {
            $properties = ['status'];
        } elseif (in_array($this->type, ['sell'])) {
            $properties = ['type', 'status', 'sub_status', 'shipping_status', 'payment_status', 'final_total'];
        } elseif (in_array($this->type, ['purchase'])) {
            $properties = ['type', 'status', 'payment_status', 'final_total'];
        } elseif (in_array($this->type, ['expense'])) {
            $properties = ['payment_status'];
        } elseif (in_array($this->type, ['sell_return'])) {
            $properties = ['type', 'payment_status', 'final_total'];
        } elseif (in_array($this->type, ['purchase_return'])) {
            $properties = ['type', 'payment_status', 'final_total'];
        }

        return $properties;
    }

    public function scopeOverDue($query)
    {
        return $query->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
    }

    public static function sell_statuses()
    {
        return [
            'final' => __('sale.final'),
            'draft' => __('sale.draft'),
            'quotation' => __('lang_v1.quotation'),
            'proforma' => __('lang_v1.proforma')
        ];
    }

    public static function sales_order_statuses($only_key_value = false)
    {
        if ($only_key_value) {
            return [
                'ordered' => __('lang_v1.ordered'),
                'partial' => __('lang_v1.partial'),
                'completed' => __('restaurant.completed')
            ];
        }
        return [
            'ordered' => [
                'label' => __('lang_v1.ordered'),
                'class' => 'bg-info'
            ],
            'partial' => [
                'label' => __('lang_v1.partial'),
                'class' => 'bg-yellow'
            ],
            'completed' => [
                'label' => __('restaurant.completed'),
                'class' => 'bg-green'
            ]
        ];
    }

    public function currency()
    {
         return $this->belongsTo("\App\Currency","currency_id");
    }

    //update state
    public static function update_status($id,$type='recieve')
    {
        
      $data           = Transaction::find($id);
      $item_          = \App\TransactionSellLine::where("transaction_id",$id)->get(); 
      $array_sell     = [] ;
      $qty_sell       = [];
      foreach($item_ as $it){
        if(!in_array($it->product_id,$array_sell)){
            array_push($array_sell,$it->product_id);
        }
      }
      foreach($array_sell as $it){
        $line  = \App\TransactionSellLine::where("transaction_id",$id)->where("product_id",$it)->sum("quantity");
        $qty_sell[$it]  = $line ;

      }
      

      $item       = \App\PurchaseLine::where("transaction_id",$id)->get(); 
      $array_purchase = [] ;
      $qty_purchase   = [];
      foreach($item as $it){
        if(!in_array($it->product_id,$array_purchase)){
            array_push($array_purchase,$it->product_id);
        }
      }
      foreach($array_purchase as $it){
        $line  = \App\PurchaseLine::where("transaction_id",$id)->where("product_id",$it)->sum("quantity");
        $qty_purchase[$it]  = $line ;

      }
      


      
      $check_all_purchase = [];
      $purchase = 0; $sell = 0;
      foreach($qty_purchase as $key => $it){
        $previous  = \App\Models\RecievedPrevious::where("transaction_id",$id)->where("product_id",$key)->sum("current_qty");
        if($it == $previous){
            $purchase = 1;
        }else{
            $purchase = 0;
        }
      }
      
      foreach($qty_sell as $key => $it){
        $delivered = \App\Models\DeliveredPrevious::where("transaction_id",$id)->where("product_id",$key)->sum("current_qty");
        if($it == $delivered){
            $sell = 1;
        }else{
            $sell = 0;
        }
      }
       
      if ($type == 'deliver') {
        if (  $sell == 1) {
            if(!empty($data)){
                if(($data->status != "ApprovedQuotation" && $data->sub_status != "proforma") || ( $data->status != "draft" && $data->status != "proforma" )){
                    $data->status =  'delivered';
                    $data->update();
                } 
            }
         }
      }else {
        if ($purchase == 1) {
            if(!empty($data)){
                $data->status =  'received';
                $data->update();
            }
        }
      }
      
    }
    public function store_from()
    {
        return $this->belongsTo('App\Models\Warehouse','store');
    }
    
    public function store_to()
    {
        return $this->belongsTo('App\Models\Warehouse','transfer_parent_id');
    }
    
     
    
    public static function delete_it($id,$business_id)
    {
         
        $transaction = Transaction::where('id', $id->id)
                                ->where('business_id', $business_id)
                                ->with(['purchase_lines'])
                                ->first();
   
        (!empty($transaction))?$transaction->delete():"";
    }
    
        // *** E-commerce 
    public static function GetLastOrderReturn($data,$client) {
        try{
            $transaction  = Transaction::where("ecommerce",1)->where("type","sell_return")->where("created_by",$client->id)->get();
            $dataAll = [];
            foreach($transaction as $i){
                $source = $i;
                $items  = \App\TransactionSellLine::where("transaction_id",$source->return_parent_id)->get();
                if($source->type == "purchase"){
                    $invoice_no = $source->ref_no;
                }else{
                    $invoice_no = $source->invoice_no;
                }
                $items_list  =  [];
                foreach($items as $I){
                    $items_list[] = [
                           "id"                   => $I->id,            
                           "product_name"         => $I->product->name,    
                           "product_code"         => $I->product->sku,            
                           "image"                => $I->product->image_url,        
                           "quantity"             => round($I->quantity,2),            
                           "category"              => $I->product->category->name,            
                           "product_category"      => $I->product->sub_category->name,
                           "unit_price"           => round($I->unit_price,2),            
                           "line_discount_amount" => round($I->line_discount_amount,2),            
                           "unit_price_inc_tax"   => round($I->unit_price_inc_tax,2),   
                           "warranty"              => (count($I->warranties)>0)?$I->warranties[0]->name . "<br>" . $I->warranties[0]->description:"",
                           "wishlist"              => ($I->product->wishlist != null)?true :false  
                           
                               
                    ];
                }
                $data  = [
                    "id"               => $source->id,
                    "type"             => $source->type,
                    "payment_status"   => $source->payment_status,
                    "status"           => $source->status,
                    "contact"          => $source->contact->name,
                    "invoice_no"       => $invoice_no,
                    "transaction_date" => $source->transaction_date,
                    "final_total"      => $source->final_total,
                    "items"            => $items_list,
                ];
                $dataAll[] = $data; 
            }
            return $dataAll; 
        }catch(Exception $e){
            return false; 
        }
    }
    

}
