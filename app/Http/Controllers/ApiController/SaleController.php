<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Unit;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\ContactUtil;
use App\Utils\TransactionUtil;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\ReferenceCount;
use App\Models\Check;
use App\Models\PaymentVoucher;
use App\Models\TransactionDelivery;
use App\Models\DeliveredPrevious;
use App\Models\Entry;
use App\Contact;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;


class SaleController extends Controller
{

    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $payment;

     /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil,ProductUtil $productUtil, TransactionUtil $transactionUtil,PaymentVoucher $payment, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->payment = $payment;
 
    }


    //*----------------------------------------*\\
    //*--------- show user sale bill ----------*\\
    //******************************************\\
    // public function index(Request $request)
    // {

    //     $api_token = request()->input("token");
    //     $api       = substr( $api_token,1);
    //     $last_api  = substr( $api_token,1,strlen($api)-1);
    //     $token     = $last_api;
    //     if(($api_token == null || $api_token == "")){
    //         abort(403, 'Unauthorized action.');
    //     }
    //     $user      = User::where("api_token",$last_api)->first();
    //     if(!$user){
    //         abort(403, 'Unauthorized action.');
    //     }
        
    //     $sales    = \App\Transaction::whereIn("type",["sale","sell_return"])->where("created_by",$user->id)->with(["sell_lines.product"])->get();
            // ->select(["id","store","contact_id","type","final_total","status","sub_status","agent_id","total_before_tax","transaction_date","invoice_no","payment_status","pattern_id","created_by"])
            // ->with(["payment_lines" => function ($i) use($user) {
            //     $i->select(["transaction_id","amount","method","payment_voucher_id"])
            //     ->with(["voucher" => function ($i) use($user) {
            //         $i->select(["id","amount"])
            //         ->with(["account_tansaction" => function ($i) use($user) {
            //             $i->where("type","debit");
            //             $i->whereIn("account_id",[$user->user_account_id,$user->user_visa_account_id]);
            //             $i->without('payment_voucher');
            //             $i->select(["payment_voucher_id","type","amount"]);
            //         }]);
            //     }]);
            // }])
            // ->with(["sell_lines" => function ($query) {
            //     $query->select(["transaction_id","product_id","quantity","unit_price_inc_tax"])
            //     ->with(["product" => function ($q) {
            //             $q->select(["id","name","tax"])
            //             ->with(["product_tax" => function ($i) {
            //                 $i->select(["id","amount"]);
            //             }]);
            //     }]);
            // }])
                                        
        
    //     $payment_bill = [];
    //     $payment_bill_id = [];
    //     foreach($sales as $it){
    //         $payment    = \App\TransactionPayment::where("transaction_id",$it->id)->whereOr("payment_voucher_id","!=",null)->select("id","amount","method","payment_voucher_id","created_by")->where("created_by",$user->id)->get();
    //         foreach($payment as $i){
    //             $cash = \App\AccountTransaction::where("payment_voucher_id",$i->payment_voucher_id)->where("type","debit")->where("account_id",$user->user_account_id)->first();
    //             $visa = \App\AccountTransaction::where("payment_voucher_id",$i->payment_voucher_id)->where("type","debit")->where("account_id",$user->user_visa_account_id)->first();
                
    //             if(!empty($visa)){
    //                     $visa_amount = $visa->amount;
    //             }else{
    //                     $visa_amount = 0;
                    
    //             }
    //             if(!empty($cash)){
    //                     $cash_amount = $cash->amount ;
                    
    //             }else{
    //                     $cash_amount = 0 ;
                    
    //             }
    //                 $i->cash_amount = $cash_amount;
    //                 $i->visa_amount = $visa_amount;
    //         }
    //         if(!in_array($it->id,$payment_bill_id)){
    //             if(count($payment)>0){
    //                 $payment_bill[$it->id] = json_decode($payment);
    //                 $payment_bill_id[] = $it->id;
    //             }
    //         }
    //     }

    //     return response()->json([
    //         "status"  => 200,
    //         "sale"    => $sales,
    //         "message" => " All Sales Shown Added By This User ",
    //         "token"   => $token,
    //         "payment_bill"   => $payment_bill
    //     ]);
    // }

    public function index(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        // $sales = \App\Transaction::where("created_by",$user->id)->whereIn("type",["sale","sell_return"])->get();
        $response = \App\Transaction::showBill($user);
        return response()->json([
            "status"                 => 200,
            "sale"                   => $response["sales"],
            "message"                => " All Sales Shown Added By This User ",
            "token"                  => $token,
            "payment_bill"           => $response["payment_bill"],
            "paymentGlobalCash"      => (isset($response["paymentGlobalCash"]))?$response["paymentGlobalCash"]:null,
            "paymentGlobalVisa"      => (isset($response["paymentGlobalVisa"]))?$response["paymentGlobalVisa"]:null,
            "paymentGlobalCheque"    => (isset($response["paymentGlobalCheque"]))?$response["paymentGlobalCheque"]:null
        ]);
    }
    
    
    //*----------------------------------------*\\
    //*------------ save sale bill ------------*\\
    //******************************************\\
    public function store(Request $request)
    {
        try{
            $token = $request->token;
             if(($token == null || $token == "")){
                abort(403, 'Unauthorized action.');
            }
            $user      = User::where("api_token",$token)->first();
            if(!$user){
                abort(403, 'Unauthorized action.');
            }
            DB::beginTransaction();
            // ........ transaction section  ......................... 
            $transaction                         = new Transaction;
            $transaction->business_id            = $request->business_id; 
            $transaction->location_id            = $request->location_id; 
            $transaction->contact_id             = $request->contact_id; 
            $transaction->store                  = $user->user_store_id; 
            $invoice_scheme_id =  1;
            $it = \App\Models\Pattern::find($user->user_pattern_id);
            if(!empty($it)){
               $invoice_scheme_id = $it->invoice_scheme;
            }
            $invoice = $this->transactionUtil->getInvoiceNumber($request->business_id, $request->status, $request->location_id, $invoice_scheme_id); 
            $transaction->invoice_no             = $invoice; 
            $transaction->type                   = $request->type; 
            $transaction->status                 = $request->status; 
            $transaction->sub_status             = $request->sub_status; 
            $date                                = \Carbon::createFromFormat('Y-m-d', $request->transaction_date)->toDateString();
            $type                                = 'project_no';
            $ref_counts                          = $this->setAndGetReferenceCount($type,$request->business_id ,$request->pattern_id );
            $reciept_nos                         = $this->generateReferenceNumber($type, $ref_counts,$request->business_id,"PRO_",$request->pattern_id);
            $transaction->transaction_date       = $date; 
            $transaction->created_by             = $request->created_by; 
            $transaction->agent_id               = isset($user->user_agent_id)?$user->user_agent_id:null; 
            $transaction->tax_id                 = $user->tax_id; 
            $transaction->discount_type          = $request->discount_type; 
            $transaction->discount_amount        = $request->discount_amount;
            $TaxRate                             = \App\TaxRate::find($user->tax_id);
            $un                                  = (!empty($TaxRate))?$TaxRate->amount:5;
            
            if($request->discount_type == "percentage"){
               $discount_i =  ($request->discount_amount * $request->total_before_tax ) / 100 ;
            }elseif($request->discount_type == "fixed_before_vat"){
                $discount_i = $request->discount_amount;
            }elseif($request->discount_type == "fixed_after_vat"){
                $discount_i = ($request->discount_amount) * 100 / (100 + $un) ;
                 
            } 
            $price_after_dis =  $request->total_before_tax - $discount_i ;
            $vat_after       =  ($request->total_before_tax - $discount_i)*$un / 100;
            $final_total     =  $vat_after + $price_after_dis;
            $transaction->is_direct_sale         = 1;
            $transaction->tax_amount             = $vat_after;  
            $transaction->total_before_tax       = $request->total_before_tax; 
            $transaction->final_total            = $final_total; 
            $transaction->return_parent_id       = $request->return_parent_id; 
            $transaction->project_no             = $reciept_nos; 
            $transaction->cost_center_id         = isset($user->user_cost_center_id)?$user->user_cost_center_id:null; 
            $transaction->pattern_id             = $user->user_pattern_id; 
            $transaction->save();
            // .... end transaction section  ..
            
            // ........ sells_line  section  ......................... 
            $sells_lines = $request->sell_lines;
            foreach($sells_lines as $it){
                $line                               = new TransactionSellLine;
                $line->store_id                     = $user->user_store_id;
                $line->product_id                   = $it["product_id"];
                $line->quantity                     = $it["quantity"];
                $line->transaction_id               = $transaction->id;
                $line->variation_id                 = $it["variation_id"];
                $line->quantity_returned            = $it["quantity_returned"];
                $line->unit_price_before_discount   = $it["unit_price_before_discount"];
                $line->unit_price                   = $it["unit_price"];
                $line->line_discount_type           = $it["line_discount_type"];
                $line->line_discount_amount         = $it["line_discount_amount"];
                $line->unit_price_inc_tax           = $it["unit_price_inc_tax"];
                $line->item_tax                     = $it["item_tax"];
                $line->sell_line_note               = $it["sell_line_note"];
                $line->save();
            }
            // .... end sells_line  section  ..
            
            
            // ........ TransactionDelivery  section  ......................... 
            $type        = 'trans_delivery';
            $ref_count   = $this->setAndGetReferenceCount($type,$transaction->business_id ,$transaction->pattern_id );
            $reciept_no  = $this->generateReferenceNumber($type, $ref_count,$transaction->business_id,"DEL_",$transaction->pattern_id);
            $tr_recieved                  =  new TransactionDelivery;
            $tr_recieved->store_id        =  $transaction->store;
            $tr_recieved->transaction_id  =  $transaction->id;
            $tr_recieved->business_id     =  $transaction->business_id ;
            $tr_recieved->reciept_no      =  $reciept_no ;
            $tr_recieved->invoice_no      =  $transaction->invoice_no;
            $tr_recieved->date            =  $transaction->transaction_date;
            $tr_recieved->status          = 'App Order';
            $tr_recieved->save();
            // .... end TransactionDelivery  section  ..
            // ........ DeliveredPrevious  section  ......................... 
            $sellLine = TransactionSellLine::where("transaction_id",$transaction->id)->get();
                $service_lines = [] ;
                foreach($sellLine as $it){
                        $service_lines[]=$it;                             
                }
            foreach($service_lines as $it){

                $prev                  =  new DeliveredPrevious;
                $prev->product_id      =  $it->product_id;
                $prev->store_id        =  $it->store_id;
                $prev->business_id     =  $it->transaction->business_id ;
                $prev->transaction_id  =  $it->transaction->id;
                $prev->unit_id         =  $it->product->unit->id;
                $prev->total_qty       =  $it->quantity;
                $prev->current_qty     =  $it->quantity;
                $prev->remain_qty      =  0;
                $prev->transaction_recieveds_id   =  $tr_recieved->id;
                $prev->product_name   =  $it->product->name;
                $prev->line_id        =  $it->id;
                 
                $prev->save();
               
                \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                \App\MovementWarehouse::movemnet_warehouse_sell($transaction,$it->product,$it->quantity,$it->store_id,$it,$prev->id);
            }
            // .... end DeliveredPrevious  section  ..
            \App\Models\ItemMove::create_sell_itemMove($transaction);
             
            // TRANSACTION ACCOUNTS 
            \App\AccountTransaction::add_sell_pos($transaction,$transaction->pattern_id);
            // TRANSACTION MAP
            \App\Models\StatusLive::insert_data_s($transaction->business_id,$transaction,"Sales Invoice");
             // ........ payment  section  ......................... 
                //  second section 
                
                if(($request->amount + $request->visa_amount) != 0 ){
                    $payment                               = new TransactionPayment;
                    $payment->business_id                  = $request->business_id;
                    $payment->store_id                     = $user->user_store_id;
                    if($request->method == "card"){
                        $payment->account_id                   = $user->user_visa_account_id;
                    }else if($request->method== "cash_visa"){
                        $payment->account_id                   = $user->user_account_id;
                    
                    }else{
                      
                        $payment->account_id                   = $user->user_account_id;
                    }
                    $payment->amount                       = $request->amount + $request->visa_amount;
                    $payment->source                       = $request->amount + $request->visa_amount;
                    $payment->transaction_id               = $transaction->id;
                    $payment->method                       = $request->method;
                    $payment->paid_on                      = $transaction->transaction_date; 
                    $payment->created_by                   = $request->created_by;
                    $type_pay                              = 'sell_payment';
                    $payment_ref_no_count                  = $this->setAndGetReferenceCount($type_pay,$request->business_id ,$request->pattern_id );
                    $payment_ref_no                        = $this->generateReferenceNumber($type_pay, $payment_ref_no_count,$request->business_id,$request->pattern_id);
                    $payment->payment_ref_no               = $payment_ref_no; 
                    $payment->save();
                    //  end second section 
                    //  first section 
                    
                    if($request->method == "payment voucher" || $request->method == "card"){
                        $check = null;
                        
                        $ref_count_pay = $this->setAndGetReferenceCount("voucher",$request->business_id,$request->pattern_id);
                        //Generate reference number
                        $ref_no_pay    = $this->generateReferenceNumber("voucher" , $ref_count_pay,$transaction->business_id,null,$transaction->pattern_id);
                        //return $this->add_main($request->cheque_type);
                        $data               =  new PaymentVoucher;
                        $data->business_id  =  $request->business_id;
                        $data->ref_no       =  $ref_no_pay;
                        if($request->method == "card"){
                            $data->account_id   =  $user->user_visa_account_id;
                            $data->amount       =  $request->visa_amount;
                        }else{
                            $data->amount       =  $request->amount;
                            $data->account_id   =  $user->user_account_id;
                        }
                        $data->contact_id   =  $request->contact_id;
                        $data->type         =  $request->type_payment;
                        $data->text         =  $request->text;
                        $data->date         =  $request->date;
                        $data->save();
                        $state     =  'debit';
                        $re_state  =  'credit';
                        if ($request->type_payment == 1 ) {
                            $state     =  'credit';
                            $re_state  =  'debit';
                        }
                        // effect cash  account 
                        $credit_data = [
                            'amount' => $data->amount,
                            'account_id' => ($request->method == "card")?$user->user_visa_account_id:$user->user_account_id,
                            'type' => $re_state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' =>  $request->created_by,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                
                        // effect contact account 
                        $account_id  =  Contact::add_account($data->contact_id,$request->business_id);
                        $credit_data = [
                            'amount' => $data->amount,
                            'account_id' => $account_id,
                            'type' => $state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' => $request->created_by ,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        $types = "voucher";
                        $ref_count_voucher  = $this->setAndGetReferenceCount("entries",$data->business_id,$data->pattern_id);
                        //Generate reference number
                        $refence_no_voucher = $this->generateReferenceNumber("entries" , $ref_count_voucher,$transaction->business_id,null,$transaction->pattern_id);
                        $entries                         = new Entry;
                        $entries->business_id            = $data->business_id;
                        $entries->refe_no_e              = 'EN_'.$refence_no_voucher;
                        $entries->ref_no_e               = $data->ref_no;
                        if($data->type == 1){
                            $entries->state              = 'Receipt Voucher';
                        }else{
                            $entries->state              = 'Payment Voucher';
                        }            
                        $entries->debit                  = $data->amount;
                        $entries->credit                 = $data->amount;
                        $entries->created_at             = $data->date;
                        $entries->updated_at             = $data->date;
                        $entries->voucher_id             = $data->id;
                        $entries->save();
                        $dat = \App\AccountTransaction::where("payment_voucher_id",$data->id)->update([
                                                            "entry_id"=>$entries->id
                                                        ]);
                        $payment_voucher = $data->id;
                    }elseif($request->method == "cheque"){
                        $payment_voucher = null;
                        $check_ref_count         =  $this->setAndGetReferenceCount("Cheque",$transaction->business_id ,$transaction->pattern_id);
                        $check_ref_no            =  $this->generateReferenceNumber("Cheque" , $check_ref_count,$transaction->business_id ,null,$transaction->pattern_id);
                        $data                    =  new Check;
                        $data->cheque_no         =  $request->cheque_number;
                        $setting                 = \App\Models\SystemAccount::first();
                        $data->account_id        =  $setting->cheque_collection;
                        $data->location_id       =  $transaction->location->id;
                        $data->write_date        =  \Carbon::createFromFormat('Y-m-d', $request->write_date)->toDateString(); 
                        $data->due_date          =  \Carbon::createFromFormat('Y-m-d', $request->due_date)->toDateString(); 
                        $data->contact_bank_id   =  $request->contact_bank_id;
                        $data->transaction_payment_id   = $payment->id;
                        $data->contact_id        =  $transaction->contact_id;
                        $data->amount            =  $request->amount;
                        $data->business_id       =  $request->business_id;
                        $data->transaction_id    =  $transaction->id;
                        $data->ref_no            =  $check_ref_no;
                        $data->account_type      =  1;
                        $data->type              = ($transaction->type == 'purchase')?1:0;
                        $data->save();
                        $type        = ($data->type == 0)?'debit':'credit';
                        $credit_data = [
                            'amount' => $request->amount,
                            'account_id' => $setting->cheque_collection ,
                            'type' => $type,
                            'sub_type' => 'deposit',
                            'operation_date' => \Carbon::createFromFormat('Y-m-d', $request->write_date)->toDateString(),
                            'created_by' => $transaction->created_by,
                            'note' => 'added cheque',
                            'check_id' => $data->id,
                            'for_repeat'=> null,
                            'transaction_id'=> $transaction->id,
                            'transaction_payment_id'=>$data->transaction_payment_id,
                        ];
            
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                       
                        Check::contact_effect($data->id,$transaction,$transaction->id,"all",$data->contact_id,$user->id);
                       
                        $check = $data->id;
                        
                        $types = "check";
                        $ref_count_check  = $this->setAndGetReferenceCount("entries",$data->business_id,$data->pattern_id);
                        //Generate reference number
                        $refence_no_check = $this->generateReferenceNumber("entries" , $ref_count_check,$transaction->business_id,null,$transaction->pattern_id);
                        $entries                         = new Entry;
                        $entries->business_id            = $data->business_id;
                        $entries->refe_no_e              = 'EN_'.$refence_no_check;
                        $entries->ref_no_e               = $data->ref_no;
                        $entries->debit                  = $data->amount;
                        $entries->credit                 = $data->amount;
                        $entries->state                  = 'Cheque';
                        $entries->check_id               = $data->id;
                        $entries->save();
                        $dat = \App\AccountTransaction::where("check_id",$data->id)->update(["entry_id"=>$entries->id]);
                        
                    }elseif($request->method == "cash_visa"){
                        $check = null;
                        $ref_count_pay = $this->setAndGetReferenceCount("voucher",$request->business_id,$request->pattern_id);
                        //Generate reference number
                        $ref_no_pay    = $this->generateReferenceNumber("voucher" , $ref_count_pay,$transaction->business_id,null,$transaction->pattern_id);
                        //return $this->add_main($request->cheque_type);
                        $data               =  new PaymentVoucher;
                        $data->business_id  =  $request->business_id;
                        $data->ref_no       =  $ref_no_pay;
                        $amoun_final        =  $request->visa_amount + $request->amount ;
                        $data->amount       =  $amoun_final  ;
                        $data->account_id   =  $user->user_account_id;
                        $data->additional_account_id   =  json_encode($user->user_visa_account_id);
                        $data->contact_id   =  $request->contact_id;
                        $data->type         =  $request->type_payment;
                        $data->text         =  $request->text;
                        $data->date         =  $request->date;
                        $data->save();
                        $state     =  'debit';
                        $re_state  =  'credit';
                        if ($request->type_payment == 1 ) {
                            $state     =  'credit';
                            $re_state  =  'debit';
                        }
                        // effect cash  account 
                        $credit_data = [
                            'amount' => $request->amount,
                            'account_id' => $user->user_account_id,
                            'type' => $re_state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' =>  $request->created_by,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        // effect visa  account 
                        $credit_data_visa = [
                            'amount' => $request->visa_amount,
                            'account_id' => $user->user_visa_account_id,
                            'type' => $re_state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' =>  $request->created_by,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit_visa = \App\AccountTransaction::createAccountTransaction($credit_data_visa);
                
                        // effect contact account 
                        $account_id  =  Contact::add_account($data->contact_id,$request->business_id);
                        $credit_data = [
                            'amount' => $data->amount,
                            'account_id' => $account_id,
                            'type' => $state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' => $request->created_by ,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        $types = "voucher";
                        $ref_count_voucher  = $this->setAndGetReferenceCount("entries",$data->business_id,$data->pattern_id);
                        //Generate reference number
                        $refence_no_voucher = $this->generateReferenceNumber("entries" , $ref_count_voucher,$transaction->business_id,null,$transaction->pattern_id);
                        $entries                         = new Entry;
                        $entries->business_id            = $data->business_id;
                        $entries->refe_no_e              = 'EN_'.$refence_no_voucher;
                        $entries->ref_no_e               = $data->ref_no;
                        if($data->type == 1){
                            $entries->state              = 'Receipt Voucher';
                        }else{
                            $entries->state              = 'Payment Voucher';
                        }            
                        $entries->debit                  = $data->amount;
                        $entries->credit                 = $data->amount;
                        $entries->created_at             = $data->date;
                        $entries->updated_at             = $data->date;
                        $entries->voucher_id             = $data->id;
                        $entries->save();
                        $dat = \App\AccountTransaction::where("payment_voucher_id",$data->id)->update([
                                                            "entry_id"=>$entries->id
                                                        ]);
                        $payment_voucher = $data->id;
                    }else{
                        $payment_voucher = null;
                        $check = null;
                    }
                    DB::commit();
                    if(round(($request->amount + $request->visa_amount),2) >= round($final_total,2) ){
                        $transaction->payment_status = 1;
                        $transaction->update();
                    }elseif(($request->amount + $request->visa_amount) == 0 || ($request->amount + $request->visa_amount) == null){
                        $transaction->payment_status = 2;
                        $transaction->update();
                    }elseif(round(($request->amount + $request->visa_amount),2) < round($final_total,2)){
                        $transaction->payment_status = 3;
                        $transaction->update();
                    }
                    //  end first section 

                    //..update 
                    $payment->payment_voucher_id           = $payment_voucher;  
                    $payment->check_id                     = $check; 
                    $payment->save();
                    //..
                }
            // .... end payment  section  ..
                
            DB::commit();

         
            $output = ['success' => 1,
                'msg' => " Added Successfully " ,
                ];
            return response()->json([
                    "status"   => 200,
                    "message"  => " Added Successfully ",
                    "token"    => $token,
                    "output"   => $output
                ]);
            
        }catch(Exception $e){
            DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                \Log::alert($e);
            $output = ['success' => 0,
                            'msg' => $e
                        ];
            return response()->json([
                            "status"   => 403,
                            "message"  => " Failed ",
                            "token"    => $token,
                            "output"   => $output

                        ]);
        }
    }
    //*----------------------------------------*\\
    //*------------ save R/sale bill ----------*\\
    //******************************************\\
    public function storeReturn(Request $request)
    {
        try{
            
            $token = $request->token;
             if(($token == null || $token == "")){
                abort(403, 'Unauthorized action.');
            }
            $user      = User::where("api_token",$token)->first();
            if(!$user){
                abort(403, 'Unauthorized action.');
            }
            DB::beginTransaction();
            // ........ transaction section  ......................... 
                $transaction                         = new Transaction;
                $transaction->business_id            = $request->business_id; 
                $transaction->location_id            = $request->location_id; 
                $transaction->contact_id             = $request->contact_id; 
                $transaction->store                  = $user->user_store_id; 
                $invoice_scheme_id =  1;
                $it = \App\Models\Pattern::find($user->user_pattern_id);
                if(!empty($it)){
                     $invoice_scheme_id = $it->invoice_scheme;
                }

                $ref_count                           = $this->setAndGetReferenceCount('sell_return', $request->business_id,$user->user_pattern_id);
                $invoice                             = $this->generateReferenceNumber('sell_return', $ref_count, $request->business_id,null,$user->user_pattern_id);

                $transaction->invoice_no             = $invoice; 
                $transaction->type                   = "sell_return"; 
                $transaction->status                 = $request->status; 
                $transaction->sub_status             = $request->sub_status; 
                $date                                = \Carbon::createFromFormat('Y-m-d', $request->transaction_date)->toDateString();
                $type                                = 'project_no';
                $ref_counts                          = $this->setAndGetReferenceCount($type,$request->business_id ,$user->user_pattern_id );
                $reciept_nos                         = $this->generateReferenceNumber($type, $ref_counts,$request->business_id,null,$user->user_pattern_id);
                $transaction->transaction_date       = $date; 
                $transaction->created_by             = $request->created_by; 
                $transaction->agent_id               = isset($user->user_agent_id)?$user->user_agent_id:null; 
                $transaction->discount_type          = $request->discount_type; 
                $transaction->discount_amount        = $request->discount_amount; 
                $transaction->tax_id                 = $user->tax_id; 
                $TaxRate                             = \App\TaxRate::find($user->tax_id);
                $un                                  = (!empty($TaxRate))?$TaxRate->amount:5;
                if($request->discount_type == "percentage"){
                   $discount_i =  ($request->discount_amount * $request->total_before_tax ) / 100 ;
                }elseif($request->discount_type == "fixed_before_vat"){
                    $discount_i = $request->discount_amount;
                }elseif($request->discount_type == "fixed_after_vat"){
                    $discount_i = ($request->discount_amount * 100) / (100 + $un) ;
                } 
                $price_after_dis                     =  $request->total_before_tax - $discount_i ;
                $vat_after                           =  ($request->total_before_tax - $discount_i)*$un / 100;
                $final_total                         =  $vat_after + $price_after_dis;
                $transaction->tax_amount             = $vat_after; 
                $transaction->total_before_tax       = $request->total_before_tax; 
                $transaction->final_total            = $final_total; 
                $transaction->project_no             = $reciept_nos; 
                $transaction->cost_center_id         = isset($user->user_cost_center_id)?$user->user_cost_center_id:null; 
                $transaction->pattern_id             = $user->user_pattern_id; 
                $transaction->save();
                $transaction->return_parent_id       = $transaction->id; 
                $transaction->save();

            // .... end transaction section  ..
            $sub_total_rt_purchase = 0;
            // ........ sells_line  section  ......................... 
            $sells_lines = $request->sell_lines;
            foreach($sells_lines as $it){
                $line                               = new TransactionSellLine;
                $line->store_id                     = $user->user_store_id;
                $line->product_id                   = $it["product_id"];
                $line->quantity                     = $it["quantity"];
                $line->transaction_id               = $transaction->id;
                $line->variation_id                 = $it["variation_id"];
                $line->quantity_returned            = $it["quantity_returned"];
                $line->unit_price_before_discount   = $it["unit_price_before_discount"];
                $line->unit_price                   = $it["unit_price"];
                $line->line_discount_type           = $it["line_discount_type"];
                $line->line_discount_amount         = $it["line_discount_amount"];
                $line->unit_price_inc_tax           = $it["unit_price_inc_tax"];
                $line->item_tax                     = $it["item_tax"];
                $line->sell_line_note               = $it["sell_line_note"];
                $line->save();
                $sub_total_rt_purchase += $it["quantity"]*$it["unit_price"];
            }
            // .... end sells_line  section  ..
             
            
            // ........ TransactionDelivery  section  ......................... 
            $type        = 'trans_delivery';
            $ref_count   = $this->setAndGetReferenceCount($type,$transaction->business_id ,$transaction->pattern_id );
            $reciept_no  = $this->generateReferenceNumber($type, $ref_count,$transaction->business_id,null,$transaction->pattern_id);
            $tr_recieved                  =  new TransactionDelivery;
            $tr_recieved->store_id        =  $transaction->store;
            $tr_recieved->transaction_id  =  $transaction->id;
            $tr_recieved->business_id     =  $transaction->business_id ;
            $tr_recieved->reciept_no      =  $reciept_no ;
            $tr_recieved->invoice_no      =  $transaction->invoice_no;
            $tr_recieved->is_returned     =  1;
            $tr_recieved->date            =  $transaction->transaction_date;
            $tr_recieved->status          = 'App Order Return';
            $tr_recieved->save();
            // .... end TransactionDelivery  section  ..
            // ........ DeliveredPrevious  section  ......................... 
            $sellLine = TransactionSellLine::where("transaction_id",$transaction->id)->get();
                $service_lines = [] ;
                foreach($sellLine as $it){
                        $service_lines[]=$it;                             
                }
            foreach($service_lines as $it){

                $prev                  =  new DeliveredPrevious;
                $prev->product_id      =  $it->product_id;
                $prev->store_id        =  $it->store_id;
                $prev->business_id     =  $it->transaction->business_id ;
                $prev->transaction_id  =  $it->transaction->id;
                $prev->unit_id         =  $it->product->unit->id;
                $prev->total_qty       =  $it->quantity;
                $prev->current_qty     =  $it->quantity;
                $prev->remain_qty      =  0;
                $prev->transaction_recieveds_id   =  $tr_recieved->id;
                $prev->product_name   =  $it->product->name;
                $prev->line_id        =  $it->id;
                $prev->is_returned    =  1;
                 
                $prev->save();
               
                \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity,$it->transaction->business_id);
                \App\MovementWarehouse::movemnet_warehouse($transaction,$it->product,$it->quantity,$it->store_id,$it,"plus",$prev->id);
            }
            // .... end DeliveredPrevious  section  ..
        
            \App\Models\ItemMove::return_sale_delivery($transaction,1);
            \App\AccountTransaction::return_sales($transaction,$discount_i,$transaction->final_total,$request->total_before_tax,$transaction->tax_amount,$user);
          
            // ........ payment  section  ......................... 
                //  second section 
                if(($request->amount+ $request->visa_amount) != 0){
                    $payment                               = new TransactionPayment;
                    $payment->business_id                  = (isset($request->business_id))?$request->business_id:null;
                    $payment->store_id                     = (isset($user->user_store_id))?$user->user_store_id:null;
                    $payment->amount                       = $request->amount+ $request->visa_amount;
                    $payment->source                       = (isset($request->amount))?$request->amount:null;
                    $payment->transaction_id               = (isset($transaction->id))?$transaction->id:null;
                    $payment->method                       = (isset($request->method))?$request->method:null;
                    $payment->paid_on                      = (isset($transaction->transaction_date))?$transaction->transaction_date:null; 
                    $payment->card_type                    = (isset($request->card_type))?$request->card_type:null; 
                    $payment->created_by                   = (isset($request->created_by))?$request->created_by:null;
                    $payment->account_id                   = (isset($user->user_account_id))?$user->user_account_id:null;
                    $type_pay                              = 'sell_payment';
                    $payment_ref_no_count                  = $this->setAndGetReferenceCount($type_pay,$request->business_id ,$request->pattern_id );
                    $payment_ref_no                        = $this->generateReferenceNumber($type_pay, $payment_ref_no_count,$request->business_id,$request->pattern_id);
                    $payment->payment_ref_no               = $payment_ref_no; 
                    $payment->save();
                    //  end second section 
                    //  first section 
                 
                    if($request->method == "payment voucher" || $request->method == "card"){
                        $check = null;
                        $ref_count_pay = $this->setAndGetReferenceCount("voucher",$request->business_id,$request->pattern_id);
                        //Generate reference number
                        $ref_no_pay    = $this->generateReferenceNumber("voucher" , $ref_count_pay,$transaction->business_id,null,$transaction->pattern_id);
                        //return $this->add_main($request->cheque_type);
                        $data                   =  new PaymentVoucher;
                        $data->business_id      =  $request->business_id;
                        $data->ref_no           =  $ref_no_pay;
                        if($request->method == "card"){
                             $data->account_id   =  $user->user_visa_account_id;
                        }else{
                            $data->account_id   =  $user->user_account_id;
                        }
                        $data->amount       =  $request->visa_amount + $request->amount;
                        $data->contact_id   =  $request->contact_id;
                        $data->type         =  $request->type_payment;
                        $data->text         =  $request->text;
                        $data->date         =  $request->date;
                        $data->save();
                       
                        $state     =  'debit';
                        $re_state  =  'credit';
                        if ($request->type_payment == 1 ) {
                            $state     =  'credit';
                            $re_state  =  'debit';
                        }
                        // effect cash  account 
                        $credit_data = [
                            'amount' => $data->amount,
                            'account_id' => ($request->method == "card")?$user->user_visa_account_id:$user->user_account_id,
                            'type' => $state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' =>  $request->created_by,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                
                        // effect contact account 
                        $account_id  =  Contact::add_account($data->contact_id,$request->business_id);
                        $credit_data = [
                            'amount' => $data->amount,
                            'account_id' => $account_id,
                            'type' => $re_state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' => $request->created_by ,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        $types = "voucher";
                        $ref_count_voucher  = $this->setAndGetReferenceCount("entries",$data->business_id,$data->pattern_id);
                        //Generate reference number
                        $refence_no_voucher = $this->generateReferenceNumber("entries" , $ref_count_voucher,$transaction->business_id,null,$transaction->pattern_id);
                        $entries                         = new Entry;
                        $entries->business_id            = $data->business_id;
                        $entries->refe_no_e              = 'EN_'.$refence_no_voucher;
                        $entries->ref_no_e               = $data->ref_no;
                        if($data->type == 1){
                            $entries->state              = 'Receipt Voucher';
                        }else{
                            $entries->state              = 'Payment Voucher';
                        }            
                        $entries->debit                  = $data->amount;
                        $entries->credit                 = $data->amount;
                        $entries->created_at             = $data->date;
                        $entries->updated_at             = $data->date;
                        $entries->voucher_id             = $data->id;
                        $entries->save();
                        $dat = \App\AccountTransaction::where("payment_voucher_id",$data->id)->update([
                                                            "entry_id"=>$entries->id
                                                        ]);
                        $payment_voucher = $data->id;
                    }elseif($request->method == "cheque"){
                        $payment_voucher = null;
                        $check_ref_count         =  $this->setAndGetReferenceCount("Cheque",$transaction->business_id ,$transaction->pattern_id);
                        $check_ref_no            =  $this->generateReferenceNumber("Cheque" , $check_ref_count,$transaction->business_id,null,$transaction->pattern_id);
                        $setting                 = \App\Models\SystemAccount::first();
                        $data                    =  new Check;
                        $data->cheque_no         =  $request->cheque_number;
                        $data->account_id        =  $setting->cheque_collection;
                        $data->location_id       =  $transaction->location->id;
                        $data->write_date        =  \Carbon::createFromFormat('Y-m-d', $request->write_date)->toDateString(); 
                        $data->due_date          =  \Carbon::createFromFormat('Y-m-d', $request->due_date)->toDateString(); 
                        $data->contact_bank_id   =  $request->contact_bank_id;
                        $data->transaction_payment_id   = $payment->id;
                        $data->contact_id        =  $transaction->contact_id;
                        $data->amount            =  $request->amount;
                        $data->business_id       =  $request->business_id;
                        $data->transaction_id    =  $transaction->id;
                        $data->ref_no            =  $check_ref_no;
                        $data->account_type      =  1;
                        $data->type              = ($transaction->type == 'purchase')?0:1;
                        $data->save();
                        $type        = ($data->type == 0)?'debit':'credit';
                        $credit_data = [
                            'amount' => $request->amount,
                            'account_id' =>$setting->cheque_collection,
                            'type' => $type,
                            'sub_type' => 'deposit',
                            'operation_date' => \Carbon::createFromFormat('Y-m-d', $request->write_date)->toDateString(),
                            'created_by' => $transaction->created_by,
                            'note' => 'added cheque',
                            'check_id' => $data->id,
                            'for_repeat'=> null,
                            'transaction_id'=> $transaction->id,
                            'transaction_payment_id'=>$data->transaction_payment_id,
                        ];
            
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        Check::contact_effect($data->id,$transaction,$transaction->id);
                        $check = $data->id;
                        
                        $types = "check";
                        $ref_count_check  = $this->setAndGetReferenceCount("entries",$data->business_id,$data->pattern_id);
                        //Generate reference number
                        $refence_no_check = $this->generateReferenceNumber("entries" , $ref_count_check,$transaction->business_id,null,$transaction->pattern_id);
                        $entries                         = new Entry;
                        $entries->business_id            = $data->business_id;
                        $entries->refe_no_e              = 'EN_'.$refence_no_check;
                        $entries->ref_no_e               = $data->ref_no;
                        $entries->debit                  = $data->amount;
                        $entries->credit                 = $data->amount;
                        $entries->state                  = 'Cheque';
                        $entries->check_id               = $data->id;
                        $entries->save();
                        $dat = \App\AccountTransaction::where("check_id",$data->id)->update(["entry_id"=>$entries->id]);
                        DB::commit();
                    }elseif($request->method == "cash_visa"){
                        $check = null;
                        $ref_count_pay = $this->setAndGetReferenceCount("voucher",$request->business_id,$request->pattern_id);
                        //Generate reference number
                        $ref_no_pay    = $this->generateReferenceNumber("voucher" , $ref_count_pay,$transaction->business_id,null,$transaction->pattern_id);
                        //return $this->add_main($request->cheque_type);
                        $data               =  new PaymentVoucher;
                        $data->business_id  =  $request->business_id;
                        $data->ref_no       =  $ref_no_pay;
                        $amoun_final        =  $request->visa_amount + $request->amount ;
                        $data->amount       =  $amoun_final  ;
                        $data->account_id   =  $user->user_account_id;
                        $data->additional_account_id   =  json_encode($user->user_visa_account_id);
                        $data->contact_id   =  $request->contact_id;
                        $data->type         =  $request->type_payment;
                        $data->text         =  $request->text;
                        $data->date         =  $request->date;
                        $data->save();
                        $state     =  'debit';
                        $re_state  =  'credit';
                        if ($request->type_payment == 1 ) {
                            $state     =  'credit';
                            $re_state  =  'debit';
                        }
                        // effect cash  account 
                        $credit_data = [
                            'amount' => $request->amount,
                            'account_id' => $user->user_account_id,
                            'type' => $state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' =>  $request->created_by,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        // effect visa  account 
                        $credit_data_visa = [
                            'amount' => $request->visa_amount,
                            'account_id' => $user->user_visa_account_id,
                            'type' => $state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' =>  $request->created_by,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit_visa = \App\AccountTransaction::createAccountTransaction($credit_data_visa);
                
                        // effect contact account 
                        $account_id  =  Contact::add_account($data->contact_id,$request->business_id);
                        $credit_data = [
                            'amount' => $data->amount,
                            'account_id' => $account_id,
                            'type' => $re_state,
                            'sub_type' => 'deposit',
                            'operation_date' => $data->date,
                            'created_by' => $request->created_by ,
                            'note' => $data->text,
                            'transaction_id'=>$transaction->id,
                            'payment_voucher_id'=>$data->id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        $types = "voucher";
                        $ref_count_voucher  = $this->setAndGetReferenceCount("entries",$data->business_id,$data->pattern_id);
                        //Generate reference number
                        $refence_no_voucher = $this->generateReferenceNumber("entries" , $ref_count_voucher,$transaction->business_id,null,$transaction->pattern_id);
                        $entries                         = new Entry;
                        $entries->business_id            = $data->business_id;
                        $entries->refe_no_e              = 'EN_'.$refence_no_voucher;
                        $entries->ref_no_e               = $data->ref_no;
                        if($data->type == 1){
                            $entries->state              = 'Receipt Voucher';
                        }else{
                            $entries->state              = 'Payment Voucher';
                        }            
                        $entries->debit                  = $data->amount;
                        $entries->credit                 = $data->amount;
                        $entries->created_at             = $data->date;
                        $entries->updated_at             = $data->date;
                        $entries->voucher_id             = $data->id;
                        $entries->save();
                        $dat = \App\AccountTransaction::where("payment_voucher_id",$data->id)->update([
                                                            "entry_id"=>$entries->id
                                                        ]);
                        $payment_voucher = $data->id;
                    }else{
                        $payment_voucher = null;
                        $check = null;
                    }
                    
                    if(round(($payment->amount),2) >= round($transaction->final_total,2) ){
                        $transaction->payment_status = 1;
                        $transaction->update();
                    }elseif($payment->amount == 0){
                        $transaction->payment_status = 2;
                        $transaction->update();
                    }elseif(round(($payment->amount),2) < round($transaction->final_total,2)){
                        $transaction->payment_status = 3;
                        $transaction->update();
                    }
                    //  end first section 

                    //..update 
                    $payment->payment_voucher_id           = $payment_voucher;  
                    $payment->check_id                     = $check; 
                    $payment->save();
                    //..
                }
            // .... end payment  section  ..
                
            DB::commit();

         
            $output = ['success' => 1,
                'msg' => " Added Successfully " ,
                ];
            return response()->json([
                    "status"   => 200,
                    "message"  => " Added Successfully ",
                    "token"    => $token,
                    "output"   => $output
                ]);
            
        }catch(Exception $e){
            DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                \Log::alert($e);
            $output = ['success' => 0,
                            'msg' => $e
                        ];
            return response()->json([
                            "status"   => 403,
                            "message"  => " Failed ",
                            "token"    => $token,
                            "output"   => $output

                        ]);
        }
    }

    //*----------------------------------------*\\
    //*------------ update sale bill ----------*\\
    //******************************************\\
    public function update(Request $request,$id)
    {
        
        return response()->json([
            "status"   => 200,
            "message"  => " Updated Successfully ",
            "token"    => $token
        ]);
    }

    //*----------------------------------------*\\
    //*-------- show Customer balance  --------*\\
    //******************************************\\
    public function showBalance(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        $user      = User::where("api_token",$token)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        $arrays=[];
        $customers=[];
        // $page  = $request->query('page', 1);
        // $skip  = $request->query('skip', 0);
        // $limit = $request->query('limit', 50);
        // $skpp  = ($page-1)*$limit;
        // $customer = \App\Contact::where("type","customer")->select("id","first_name")->skip($skpp)->take($limit)->get();
        $customer = \App\Contact::where("type","customer")->select("id","first_name")->get();
        // $customertotal = \App\Contact::where('type',"customer")->count();
        $balancess = \App\AccountTransaction::whereHas("account",function($query){
                                                    $query->whereNotNull("contact_id");
                                                    $query->whereHas("contact",function($q){
                                                        $q->where("type","customer");
                                                    });
                                                })
                                                ->where("for_repeat","=",null)
                                                ->select(\DB::raw('(SUM(CASE WHEN type = "debit" THEN amount * -1 ELSE 0 END) + SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END)) AS balance'),"account_id")
                                                ->groupby("account_id")
                                                ->get();
        $customers = [];
        foreach($balancess as $i ){
            $account    = \App\Account::find($i->account_id);
            $contact    = \App\Contact::find($account->contact_id);
            $bal_text   =  $i->balance;
            $bl  =  'DR';
                if ($i->balance < 0 ) {
                    $bl = 'CR';
                    $bal_text  =  abs($i->balance) ;
                    $customers[] = ["id"=> $contact->id,"name"=>$contact->first_name,"balance"=>$bal_text, "type"=>$bl];
                }elseif($i->balance == 0){
                    $bl = '';
                }else{
                    $customers[] = ["id"=> $contact->id,"name"=>$contact->first_name,"balance"=>$bal_text, "type"=>$bl];
                    
                }
        }
         // Create pagination URLs for next and previous pages
        //  $prevPage = $page > 1 ? $page - 1 : null;
        //  $nextPage = $page < $totalPages ? $page + 1 : null;
         $count_main = count($customers);

        return response()->json([
            "status"   => 200,
            "customer" => $customers, 
            "message"  => " All Customer Shown With Balance ",
            "totalRows"     => $count_main ,
            // 'current_page'  => $page,
            // 'last_page'     => $totalPages,
            // 'limit'         => 50,
            // 'prev_page_url' => $prevPage ? "/api/sale/customer-balance?page=$prevPage" : null,
            // 'next_page_url' => $nextPage ? "/api/sale/customer-balance?page=$nextPage" : null,
           
        ]);
    }
    
    //*----------------------------------------*\\
    //*------------ show product   ------------*\\
    //******************************************\\
    public function product(Request $request)
    {
        $api_token  = request()->input("token");
        $contact_id = request()->input("contact_id");
        $api        = substr( $api_token,1);
        $last_api   = substr( $api_token,1,strlen($api)-1);
        $token      = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }      
        $user       = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
       
        $product = \App\Product::join("variations as vr","products.id","vr.product_id")->select("products.id","products.tax","products.sku as barcode","products.name","products.image","products.product_description","vr.default_sell_price","vr.sell_price_inc_tax")->get();
        
       
        $product_last = [];
        foreach($product as $it){
            $prs = json_decode($it);
            if(isset($contact_id) && $contact_id != null){
                $contacts_price = TransactionSellLine::orderby("id","desc")->where("product_id",$it->id)->whereHas("transaction",function($query) use($contact_id){
                    $query->where("contact_id" , $contact_id);
                })->select()->first();
            }
            
            if(isset($contacts_price) && $contact_id != null){
                if($contacts_price != null){
                 if($it->tax!=null){$tax_rates = \App\TaxRate::where('id', $it->tax)->select(['amount'])->first();$tax_ = $tax_rates->amount ;}else{$tax_ = 0;}
               
                      $price = ($contacts_price->unit_price_inc_tax * 100) / (100  + $tax_rates->amount);                
                    
                }
            }
            
           
            $array_exc  =  [
                                "prd_price"=>doubleval($it->default_sell_price),
                                "customer_price" => (isset($price))?doubleval($price):0,
                                "Whole Price"    => doubleval(100),
                                "Retail Price"   => doubleval(200),
                                "Minimum Price"  => doubleval(40),
                                "Last Price"     => doubleval(600)
                                
                            ];
            $array_inc  =  [
                                "prd_price"      => doubleval($it->sell_price_inc_tax),
                                "customer_price" => (isset($contacts_price->unit_price_inc_tax))? doubleval($contacts_price->unit_price_inc_tax):0 ,
                                "Whole Price"    => doubleval(105),
                                "Retail Price"   => doubleval(210),
                                "Minimum Price"  => doubleval(20),
                                "Last Price"     => doubleval(630)
                            ];
            $prs->Exclude                 =  $array_exc  ;
            $prs->Include                 =  $array_inc  ;
          
            $product_last[] = $prs;
        }
        $count = count($product_last);
        return response()->json([
            "status"    => 200,
            "product"   => $product_last ,
            "message"   => " All Product Shown ",
            "token"     => $token,
            "count_of_product"  => $count

        ]);
    }
    //*----------------------------------------*\\
    //*------- show account statment ----------*\\
    //******************************************\\
    public function statement(Request $request)
    {
        $api_token   = request()->input("token");
        $contact_id  = request()->input("contact_id");
        $transaction = request()->input("transaction_id");
        $api         = substr( $api_token,1);
        $last_api    = substr( $api_token,1,strlen($api)-1);
        $token       = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user        = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        $account = \App\Account::where("contact_id",$contact_id)->first();
        $id      = (!empty($account))?($account->id):(null);
        $ledger  = \App\AccountTransaction::where("transaction_id",$transaction)->where("amount","!=",0)->whereHas("account",function($query){
                                                $query->where("cost_center",0);
                                        })->where("for_repeat",null)
                                        ->with(["account","entries"])
                                        ->get();
        
        
        $count = count($ledger);
        return response()->json([
            "status"      => 200,
            "statements"  => $ledger ,
            "message"     => " All Statement Shown ",
            "token"       => $token,
 
        ]);
    }
    //*----------------------------------------*\\
    //*------------ show customer   -----------*\\
    //******************************************\\
    public function customer(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        
        $customer = \App\Contact::where("type","customer")->select("id","first_name")->get();
        $count    = count($customer);
        return response()->json([
            "status"   => 200,
            "customer" => $customer,
            "message"  => " All Customer Shown ",
            "token"    => $token,
            "count_of_customer"  => $count
        ]);
    }

    

    //*----------------------------------------*\\
    //*------------ show patterns   -----------*\\
    //******************************************\\
    public function pattern(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        $setting  = \App\Business::find($user->business_id); 
        $pattern  = \App\Models\Pattern::where("id",$setting->app_pattern_id)->select("id","name")->get();
        $count    = count($pattern);
        return response()->json([
            "status"   => 200,
            "pattern"  => $pattern,
            "message"  => " All Pattern Shown ",
            "token"    => $token,
            "count_of_pattern"  => $count
        ]);
    }

 
    //*----------------------------------------*\\
    //*------------ show inventory   -----------*\\
    //******************************************\\
    public function inventory(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        
        if(($api_token == null || $api_token == "")){
                abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        $array = [];
        $product_id = [];
        $Product    = \App\Product::select("id","name","image")->get();
        $variation  = [];
        $cost       = [];
        
        $price  = TransactionSellLine::whereNotNull("product_id")->select("product_id",DB::raw("IF( COUNT(id) != 0 ,SUM(unit_price)/COUNT(id),0) as price"),"product_id")->groupby("product_id")->get();
        foreach($price as $it){
            $array[$it->product_id] = $it->price;
            $product_id[] = $it->product_id;
        }
        $products = \App\Variation::join("products as pr", 'pr.id', '=', 'variations.product_id')->whereNotIn("pr.id",$product_id)->select("sell_price_inc_tax as price","pr.id as id","pr.name as name","pr.image as image")->get();
        foreach($products as $i){
            $array[$i->id] = $i->price;
        }
        
        $warehouse  = \App\Models\WarehouseInfo::where("store_id",$user->user_store_id)->select("store_id","product_id","product_qty")->get();
        $count    = count($warehouse);
        return response()->json([
            "status"      => 200,
            "product"     => $Product,
            "warehouse"   => $warehouse,
            "variation"   => $array,
            "message"  => " All Warehouse Shown ",
            "token"    => $token,
       
        ]);
    }
    
    //*----------------------------------------*\\
    //*------------ show vat        -----------*\\
    //******************************************\\
    public function vat(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        $vat  = \App\TaxRate::select("id","name","amount")->get();
        $count    = count($vat);
        return response()->json([
            "status"   => 200,
            "vat"      => $vat,
            "message"  => " All Vat Shown ",
            "token"    => $token,
            "count_of_vat"  => $count
        ]);
    }
    //*----------------------------------------*\\
    //*------------ show user       -----------*\\
    //******************************************\\
    public function user(Request $request)
    {
        // $api_token = request()->input("token");
        // $api       = substr( $api_token,1);
        // $last_api  = substr( $api_token,1,strlen($api)-1);
        // $token     = $last_api;
        // $user      = User::where("api_token",$last_api)->first();
        // if(!$user){
        //     abort(403, 'Unauthorized action.');
        // }
        $user     = \App\User::where("allow_login",1)->whereNotNull("user_account_id")->select("id","username","user_account_id","user_pattern_id","business_id","tax_id","include")->get();
        $userinfo = [];
        foreach($user as $i){
             
             if($i->tax_id!=null){
                $tax_amount = \App\TaxRate::where("business_id",$i->business->id)->where("id",$i->tax_id)->first();
                $amount_tax = $tax_amount->amount;
             }else{
                $amount_tax = null;
                 
             }
            $userinfo[] = [
                    "id"              => $i->id,
                    "username"        => $i->username,
                    "user_account_id" => $i->user_account_id,
                    "user_pattern_id" => $i->user_pattern_id,
                    "Whole Price"     => true,
                    "Retail Price"    => true,
                    "Last Price"      => false,
                    "Minimum Price"   => true,
                    "can_edit"        => true,
                    "tax_number"      => $i->business->tax_number_1,
                    "tax_amount"      => $amount_tax,
                    "product_price"   => ($i->include==0)?"Exclude":"Include",
                ];
        }
        $count    = count($user);
        return response()->json([
            "status"   => 200,
            "users" => $userinfo,
            "message"  => " All User Shown ",
            "count_of_user"  => $count

        ]);
    }
    //*----------------------------------------*\\
    //*------------ show cost center ----------*\\
    //******************************************\\
    public function cost_center(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        
        $cost_centers =  \App\Account::where("cost_center",1)->select("id","name")->get();
        $count        = count($cost_centers);

        return response()->json([
            "status"      => 200,
            "cost_center" => $cost_centers,
            "message"     => " All Cost Center Shown ",
            "token"       => $token,
            "count_of_cost_center"  => $count
        ]);
    }
    //*----------------------------------------*\\
    //*------------ show agent       ----------*\\
    //******************************************\\
    public function agent(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        
        $agent = \App\User::where('is_cmmsn_agnt',1)->select('id',
        DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))->get();
        $count = count($agent);
        return response()->json([
            "status"      => 200,
            "agent"       => $agent,
            "message"     => " All Agents Shown ",
            "token"       => $token,
            "count_of_agent"   => $count
        ]);
    }
    //*----------------------------------------*\\
    //*------ show contact_bank_id   ----------*\\
    //******************************************\\
    public function contact_bank(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        
        $contact_bank = \App\Models\ContactBank::select("id","name")->get();
        $count = count($contact_bank);
        return response()->json([
            "status"          => 200,
            "contact_bank"    => $contact_bank,
            "message"         => " All Contact Bank Shown ",
            "token"           => $token,
            "count_of_contact_bank"  => $count
        ]);
    }

    //*----------------------------------------*\\
    //*-------    add customers         -------*\\
    //******************************************\\
    public function addCustomer(Request $request)
    {
        $api_token = request()->input("token");
        $token     = $api_token;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$token)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        \DB::beginTransaction();
        
        $input                = $request->only([
                                        'first_name', 
                                        'tax_number',  
                                        'mobile',  
                                        'address_line_1'
                                        ]);
        $customer_previous = \App\Contact::where("name",trim($request->first_name))->first();                                
        if(!empty($customer_previous)){
            return response([
                "status"  => "403",
                "message" => "Invalid",
                "result"  => 0
            ],403);                                
        }                                
        $input['contact_id']      = null;
        $input['name']            = $request->first_name;
        $input['type']            = "customer";
        $input['business_id']     = $user->business_id;
        $input['created_by']      = $user->id;
        $output                   = $this->contactUtil->createNewContact($input);
        
        $activity                 = new Activity();
        $activity->business_id    = Null;
        $activity->log_name       = "default";
        $activity->description    = "added";
        $activity->subject_id     = $user->id;
        $activity->subject_type   = "App\Contact";
        $activity->causer_id      = $user->id;
        $activity->causer_type    = "App\Contact";
        $activity->properties     = [];
        $activity->save();

        $last                     = \App\Contact::OrderBy('id','desc')->first(); 
        \App\Contact::add_account($last->id,$user->business_id);

        \DB::commit();

        return response()->json([
            "status"      => 200,
            "message"     => " Added Successfully ",
        ]);
    }
    //*----------------------------------------*\\
    //*------------    show store    ----------*\\
    //******************************************\\
    public function stores(Request $request)
    {
        $api_token = request()->input("token");
        $api       = substr( $api_token,1);
        $last_api  = substr( $api_token,1,strlen($api)-1);
        $token     = $last_api;
        if(($api_token == null || $api_token == "")){
            abort(403, 'Unauthorized action.');
        }
        $user      = User::where("api_token",$last_api)->first();
        if(!$user){
            abort(403, 'Unauthorized action.');
        }
        $setting  = \App\Business::find($user->business_id); 
        $store    = \App\Models\Warehouse::where("status",1)->where("id",$setting->app_store_id)->select("id","name")->get();
        $count    = count($store);
        return response()->json([
            "status"      => 200,
            "stores"      => $store,
            "message"     => " All Stores Shown ",
            "token"       => $token,
            "count_of_warehouses"   => $count
        ]);
    }

    //*----------------------------------------*\\
    //*--------- delete sale bill -------------*\\
    //******************************************\\
    public function delete(Request $request,$id)
    {
         
        return response()->json([
            "status"   => 200,
            "message"  => " Deleted Successfully ",
            "token"    => $token
        ]);
    }
    //*----------------------------------------*\\
    //*--------- references  bill -------------*\\
    //******************************************\\
    public function setAndGetReferenceCount($type,$business_id,$pattern)
    {
        $ref = ReferenceCount::where('ref_type', $type)
                          ->where('business_id', $business_id)
                          ->where('pattern_id', $pattern)
                          ->first();
        if (!empty($ref)) {
            $ref->ref_count += 1;
            $ref->save();
            return $ref->ref_count;
        } else {
            $new_ref = ReferenceCount::create([
                'ref_type' => $type,
                'business_id' => $business_id,
                'pattern_id' => $pattern,
                'ref_count' => 1
            ]);
            return $new_ref->ref_count;
        }
    }
    //*----------------------------------------*\\
    //*--------- references  bill -------------*\\
    //******************************************\\
    public function generateReferenceNumber($type, $ref_count, $business_id = null, $default_prefix = null,$pattern =null)
    {
        if (!empty($default_prefix)) {
            $prefix = $default_prefix;
        }
        $ref_digits =  str_pad($ref_count, 5, 0, STR_PAD_LEFT);
        if(!isset($prefix)){
                $prefix = "";
        }
        if (!in_array($type, ['contacts', 'business_location', 'username' ,"supplier","customer"   ])) {
            $ref_year = \Carbon::now()->year;
           
            $ref_number = $prefix . $ref_year . '/' . $ref_digits;
            
        } else {
             
            $ref_number = $prefix . $ref_digits;
        }
        return  $ref_number;
    }
}
