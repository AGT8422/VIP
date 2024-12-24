<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentVoucher;
use App\BusinessLocation;
use App\Business;
use App\TransactionPayment;
use App\Contact;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Account;
use App\Models\ContactBank;
use App\Models\Entry;
use App\Utils\Util;
use DB;

class PaymentVoucherController extends Controller
{
    public function __construct(ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
    }
    public function index(Request $request)
    {
        $business_id        = request()->session()->get('user.business_id');
        $allData            =  PaymentVoucher::OrderBy('id','desc')->where('business_id',$business_id)
                                        ->where(function($query) use($request){
                                            if ($request->contact_id) {
                                                $query->where('contact_id',$request->contact_id);
                                            }
                                            if ($request->voucher_type) {
                                                $query->where('type',$request->voucher_type);
                                            }
                                            if ($request->name) {
                                                $query->where('ref_no','LIKE','%'.$request->name.'%');
                                            }
                                            if ($request->date_from) {
                                                $query->whereDate('date','>=',$request->date_from);
                                            }
                                            if ($request->date_to) {
                                                $query->whereDate('date','<=',$request->date_to);
                                            }
                                        })->paginate(30);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $contacts           = Contact::contactDropdown($business_id, false, false);
        $types              = PaymentVoucher::types();
        $accounts           = Account::items();
       
        return view('voucher_payments.index')
                ->with('title',trans('home.Vouchers List'))
                ->with(compact('allData','business_locations','contacts','types','accounts'));
    }
    public function add(Request $request)
    {
        $business_id        = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $contacts           = Contact::suppliers();
        if ($request->type == 1) {
            $contacts       = Contact::customers();
        }
        # Accounts 
        $type               =  $request->type;
        $banks              =  ContactBank::items();
        $title              =  (app('request')->input('type') == 1)?'Receipt voucher':'Payment Voucher'; 
        $accounts           =  Account::accounts($business_id);
        $currency           =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies         = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $accountsS          = Account::where("business_id",$business_id)->where('is_closed',0)->get();
        $account_list       = [];
        foreach($accountsS as $act){
            $account_list[$act->id] = $act->name . " || " . $act->account_number;
        }
        return view('voucher_payments.add')
                ->with(compact('business_locations','currencies','account_list','contacts','accounts','banks','type','title'))
                ;
    }
    public function post_add(Request  $request)
    {

        # note 0=> supplier , 1 =>customer
        DB::beginTransaction();
        $business_id      = request()->session()->get('user.business_id');
        # Generate reference number
        $ref_count             =  $this->productUtil->setAndGetReferenceCount("voucher");
        $ref_no                =  $this->productUtil->generateReferenceNumber("voucher" , $ref_count);
        # .........................................
        $company_name      = request()->session()->get("user_main.domain");
        $document_expense = [];
        $referencesNewStyle = str_replace('/', '-', $ref_no );
        if ($request->hasFile('document_expense')) { $count_doc1 = 1;
            foreach ($request->file('document_expense') as $file) {
                #................
                if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                    if ($file->getSize() <= config('constants.document_size_limit')){ 
                        $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                        $file->move('uploads/companies/'.$company_name.'/documents/voucher',$file_name_m);
                        $file_name =  'uploads/companies/'.$company_name.'/documents/voucher/'. $file_name_m;
                    }
                }else{
                    if ($file->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                        $Data         = getimagesize($file);
                        $width         = $Data[0];
                        $height        = $Data[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2; 
                        $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        $file_name =  'uploads/companies/'.$company_name.'/documents/voucher/'. $new_file_name;
                        // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\voucher\\$new_file_name"),20)) {
                        //     $uploaded_file_name = $new_file_name;
                        // }
                        $public_path = public_path('uploads/companies/'.$company_name.'/documents/voucher');
                        if (!file_exists($public_path)) {
                            mkdir($public_path, 0755, true);
                        }
                        if ($imgs->save($public_path ."/" . $new_file_name)) {
                            $uploaded_file_name = $new_file_name;
                        }
                            
                    }
                }
                #................
                array_push($document_expense,$file_name);
            }
        }
        $data                  =  new PaymentVoucher;
        $data->business_id     =  $business_id;
        $data->ref_no          =  $ref_no;
        $data->amount          =  $request->amount;
        $data->account_id      =  $request->account_id;
        $data->contact_id      =  $request->contact_id;
        $data->type            =  $request->type;
        $data->currency_id     =  $request->currency_id;
        $data->currency_amount =  $request->amount_currency;
        $data->exchange_price  =  $request->currency_id_amount;
        $data->text            =  $request->text;
        $data->date            =  $request->date;
        $data->document        =  json_encode($document_expense) ;
        $data->account_type    =  1;
        $data->save();
        #..........................................
        $this->effect_account($data->id,$data->type);
        #..........................................
        $bills                 =  $request->only([
                'bill_id','bill_amount'
        ]);
        if ($request->bill_id) {
             \App\Services\PaymentVoucher\Bill::pay_transaction($data->id,$bills);
        }
        $type="voucher";
        \App\Models\Entry::create_entries($data,$type);
        DB::commit();
        return redirect('payment-voucher')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function edit($id)
    {
        $business_id        = request()->session()->get('user.business_id');
        $data               = PaymentVoucher::find($id);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $business           = Business::find($business_id);
        $ID_TYPE            = 1;
        $contacts           = Contact::suppliers();
        if ($data->type == 1) {
            $contacts       = Contact::customers();
            $ID_TYPE        = 0;
        }
        $contacts = Contact::customersSuppliers();
        # Accounts 
        $banks              = ContactBank::items();
        $title              = ($data->type == 1)?trans('home.Receipt voucher'):trans('home.Payment voucher'); 
        $accounts           = Account::accounts($business_id);
        $currency           = \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies         = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $accountsS          = Account::where("business_id",$business_id)->where('is_closed',0)->get();
        $account_list       = [];
        foreach($accountsS as $act){
            $account_list[$act->id] = $act->name . " || " . $act->account_number;
        }
        return view('voucher_payments.edit')
                ->with(compact('business_locations','business','currencies','account_list','ID_TYPE','contacts','accounts','banks','title','data'))
                ;
    }
    public function post_edit(Request $request,$id)
    {
        DB::beginTransaction();
        $data                  =  PaymentVoucher::find($id);
        $old_id                =  $data->account_id;
        $old_contact           =  $data->contact_id;
        $old_type              =  $data->account_type;
        $data->currency_id     =  $request->currency_id;
        $data->currency_amount =  $request->amount_currency;
        $data->exchange_price  =  $request->currency_id_amount;
        $data->amount          =  $request->amount;
        $data->account_id      =  $request->account_id;
        $data->contact_id      =  $request->contact_id;
        $data->text            =  $request->text;
        $data->date            =  $request->date;
        $data->account_type    =  1;
        $old_document          =  $data->document;
        #..........................................
        $company_name      = request()->session()->get("user_main.domain");
        if($old_document == null){  $old_document = [];  } 
        if ($request->hasFile('document_expense')) { $count_doc2 = 1;
            $referencesNewStyle = str_replace('/', '-', $data->ref_no  );
            foreach ($request->file('document_expense') as $file) {
                #................
                if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                    if ($file->getSize() <= config('constants.document_size_limit')){ 
                        $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$count_doc2++.'_'.$file->getClientOriginalName();
                        $file->move('uploads/companies/'.$company_name.'/documents/voucher',$file_name_m);
                        $file_name =  'uploads/companies/'.$company_name.'/documents/voucher/'. $file_name_m;
                    }
                }else{
                    if ($file->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time().'_'.$referencesNewStyle.'_'.$count_doc2++.'_'.$file->getClientOriginalName();
                        $Data         = getimagesize($file);
                        $width         = $Data[0];
                        $height        = $Data[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2; 
                        $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        $file_name =  'uploads/companies/'.$company_name.'/documents/voucher/'. $new_file_name;
                        // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\voucher\\$new_file_name"),20)) {
                        //     $uploaded_file_name = $new_file_name;
                        // }
                        $public_path = public_path('uploads/companies/'.$company_name.'/documents/voucher');
                        if (!file_exists($public_path)) {
                            mkdir($public_path, 0755, true);
                        }
                        if ($imgs->save($public_path ."/" . $new_file_name)) {
                            $uploaded_file_name = $new_file_name;
                        }
                            
                    }
                }
                #................
                 array_push($old_document,$file_name);
            }
        }
        if(json_encode($old_document)!="[]"){
            $data->document        =  json_encode($old_document) ;
        }
        #..........................................
        $data->save();
        $type      =  $data->type;
        $state     =  'debit';
        $re_state  =  'credit';
        if ($type == 1 ) {
            $state     =  'credit';
            $re_state  =  'debit';
        }
        $voucherAccountTransaction  = \App\AccountTransaction::where('payment_voucher_id',$id)->where("type",$re_state)->where('account_id',$old_id)->get();
        foreach($voucherAccountTransaction as $items){
            # .......................................
            $account_old            = $items->account_id;
            $account_new            = $request->account_id;
            $account_date_old       = $items->operation_date;
            $account_date_new       = $request->date;
            # .......................................
            $items->amount          = $request->amount;
            $items->operation_date  = $request->date;
            $items->account_id      = $request->account_id;
            $items->update();
            # .......................................
            $accountONE             = \App\Account::find($account_old);
            $accountTWO             = \App\Account::find($account_new);
            if($accountONE->cost_center!= 1){ \App\AccountTransaction::nextRecords($accountONE->id,$data->business_id,$account_date_old); }
            if($accountTWO->cost_center!= 1){ \App\AccountTransaction::nextRecords($accountTWO->id,$data->business_id,$account_date_new); }
        } 

        if($old_type == 0){
            $old_account_id     =  \App\Account::where('contact_id',$old_contact)->first();
        }else{
            $old_account_id     =  \App\Account::where('id',$old_contact)->first();
        }
        
        $accountAccountTransactionOld   = \App\AccountTransaction::where('payment_voucher_id',$id)->where("type",$state)->where('account_id',$old_account_id->id)->get();
        foreach($accountAccountTransactionOld as $items){
            # .......................................
            $account_old            = $items->account_id;
            $account_new            = $request->contact_id;
            $account_date_old       = $items->operation_date;
            $account_date_new       = $request->date;
            # .......................................
            $items->amount          = $request->amount;
            $items->operation_date  = $request->date;
            $items->account_id      = $request->contact_id;
            $items->update();
            # .......................................
            $accountONE             = \App\Account::find($account_old);
            $accountTWO             = \App\Account::find($account_new);
            if($accountONE->cost_center!= 1){ \App\AccountTransaction::nextRecords($accountONE->id,$data->business_id,$account_date_old); }
            if($accountTWO->cost_center!= 1){ \App\AccountTransaction::nextRecords($accountTWO->id,$data->business_id,$account_date_new); }
        } 
        if(count($voucherAccountTransaction)==0){
            $voucherAccountTransaction   = \App\AccountTransaction::where('payment_voucher_id',$id)->where("type",$state)->where('account_id',$old_id)->delete();
             # effect cash  account 
            $credit_data = [
                'amount'             => $data->amount,
                'account_id'         => $data->account_id,
                'type'               => $re_state,
                'sub_type'           => 'deposit',
                'operation_date'     => $data->date,
                'created_by'         => session()->get('user.id'),
                'note'               => $data->text,
                'payment_voucher_id' => $id
            ];
            $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
            $_account = \App\Account::find($data->account_id);
            if($_account->cost_center!= 1){
                \App\AccountTransaction::nextRecords($_account->id,$data->business_id,$data->date);
            }
        }                           
        $bills                       =  $request->only([
            'bill_id','bill_amount','old_bill_id','old_bill_amount','payment_id'
        ]);
        \App\Services\PaymentVoucher\Bill::update_pay_transaction($data->id,$bills);
        $payment = \App\TransactionPayment::where("payment_voucher_id",$id)->first();
        if($payment){
            $old_payment = $payment->replicate();
            // $parent      = \App\Models\ParentArchive::save_payment_parent($payment->id,"Edit",$old_payment);
        }

        if(!empty($payment)){
       
            $sum           =  \App\TransactionPayment::where('transaction_id',$payment->transaction_id)->sum("amount");
            $final         =  $sum - $payment->amount ;
            $transaction   =  \App\Transaction::find($payment->transaction_id);
            $total_bill    =  round($transaction->final_total,config("constants.currency_precision"));
            //...... here no previous payment 
            if($final == 0){
                $margin_total = $total_bill - round($request->amount,config("constants.currency_precision"));
                if($margin_total < 0 || $margin_total == 0){
                    $payment_amount_final = round($total_bill,config("constants.currency_precision"));
                }elseif($margin_total > 0){
                    $payment_amount_final = round($request->amount,config("constants.currency_precision"));
                } 
            }else{
                $margin_total = $total_bill - $final;
                $now          = round($margin_total,config("constants.currency_precision")) - round($request->amount,config("constants.currency_precision")); 
                if($now < 0 || $now == 0){
                    $payment_amount_final = round($total_bill,config("constants.currency_precision"));
                }elseif($margin_total > 0){
                    $payment_amount_final = round($now,config("constants.currency_precision"));
                } 
            }
            $payment->payment_for = $data->contact_id;
            $payment->amount      = $request->amount;
            $payment->source      = $payment_amount_final;
            $payment->save();
            if(isset($request->separate) && $request->separate != null){
                
                $transaction->final_total       = $request->amount;
                $tax_amount                     =  \App\TaxRate::find($transaction->tax_id);
                $value                          = ($tax_amount)?$tax_amount->amount:0;
                $transaction->tax_amount        =  round(($request->amount * (($value))/(100+$value)),config("constants.currency_precision"));
                $transaction->total_before_tax  = $request->amount - round((($request->amount) * (($value))/(100 + $value)),config("constants.currency_precision")) ;
                $transaction->update();
                $old_trans                      = $transaction->cost_center_id;
                $old_account                    = $transaction->contact_id;
                $old_discount                   = $transaction->discount_amount;
                $old_tax                        = $transaction->tax_amount;
                $old_pattern_id                 = $transaction->pattern_id;
                $parent                         = \App\Transaction::find($transaction->separate_parent); 
                $total_parent                   = $parent->final_total;
                $sum                            = 0;
                $allTransaction                 = \App\Transaction::where("separate_parent",$transaction->separate_parent)->get();
                foreach($allTransaction as $one_pay){
                $sum    +=   $one_pay->final_total; 
                }
                if(!empty($total_parent)){
                    $parent_balance = $parent->final_total;
                    $status  = 'due';
                    if ($parent_balance <= $sum) {
                        $status = 'paid';
                    } elseif ($sum > 0 && $parent_balance > $sum) {
                        $status = 'partial';
                    }
                    $parent->payment_status = $status;
                    $parent->update();
                }
                \App\AccountTransaction::update_sell_pos_($transaction,null,$old_trans,$old_account,$old_discount,$old_tax,$old_pattern_id,$old_pattern_id);
            }

        }
        // if(!empty($payment)){

        //     $sum           =  \App\TransactionPayment::where('transaction_id',$payment->transaction_id)->sum("amount");
        //     $final         =  $sum - $payment->amount ;
        //     $transaction   =  \App\Transaction::find($payment->transaction_id);
        //     $total_bill    =  round($transaction->final_total,2);
        //     //...... here no previous payment 
        //     if($final == 0){
        //         $margin_total = $total_bill - round($request->amount,2);
        //         if($margin_total < 0 || $margin_total == 0){
        //             $payment_amount_final = round($total_bill,2);
        //         }elseif($margin_total > 0){
        //             $payment_amount_final = round($request->amount,2);
        //         } 
        //     }else{
        //         $margin_total = $total_bill - $final;
        //         $now          = round($margin_total,2) - round($request->amount,2); 
        //         if($now < 0 || $now == 0){
        //             $payment_amount_final = round($total_bill,2);
        //         }elseif($margin_total > 0){
        //             $payment_amount_final = round($now,2);
        //         } 
        //     }
        //     $payment->source = $payment_amount_final;
        //     $payment->save();

        // }
        #..........................................................................
        DB::commit();
        return redirect('payment-voucher')
                   ->with('yes',trans('home.Done Successfully'));
    }
    public function delete($id)
    {
        

        $data    =  PaymentVoucher::find($id);
        DB::beginTransaction();
        
        if ($data) {
            $entries = [];
            $line_transaction = \App\AccountTransaction::where('payment_voucher_id',$id)->get();
            foreach($line_transaction as $i){
                if(!in_array($i->entry_id,$entries)){
                    $entries[] = $i->entry_id;
                }
                $action_date = $i->operation_date;
                $account     = \App\Account::find($i->account_id);
                $i->delete();
                if($account->cost_center!=1){
                    \App\AccountTransaction::nextRecords($account->id,$data->business_id,$action_date);
                }
                 
            }
           
            $payment =  \App\TransactionPayment::where("payment_voucher_id",$id)->first();
            
            if(!empty($payment)){
                if($payment->is_invoice == 1){
                    $payment->amount = 0;
                    $payment->update();
                    $total_paid      = $payment->amount;
                    $sum             = 0;
                    $final_amount    = \App\Transaction::find($payment->transaction_id);
                    $parent          = \App\Transaction::find($final_amount->separate_parent);
                    $total_parent    = $parent->final_total;
                    $allTransaction  = \App\Transaction::where("id","!=",$payment->transaction_id)->where("separate_parent",$final_amount->separate_parent)->get();
                    $entries[]       = \App\AccountTransaction::where("transaction_id",$payment->transaction_id)->first()->entry_id; 
                    foreach($allTransaction as $one_pay){ $sum    +=   $one_pay->amount;  }
                    if(!empty($total_parent)){
                        $parent_balance = $parent->final_total;
                            $status     = 'due';
                        if ($parent_balance <= $sum) {
                            $status     = 'paid';
                        } elseif ($sum > 0 && $parent_balance > $sum) {
                            $status     = 'partial';
                        }
                        $parent->payment_status = $status;
                        $parent->update();
                    }
                    if(!empty($final_amount)){
                            $balance  = $final_amount->final_total;
                            $status   = 'due';
                        if ($balance <= $total_paid) {
                            $status   = 'paid';
                        } elseif ($total_paid > 0 && $balance > $total_paid) {
                            $status   = 'partial';
                        }
                        $final_amount->payment_status = $status;
                        $final_amount->delete();
                        \App\AccountTransaction::where('transaction_id',$payment->transaction_id)->delete();
                    } 
                    $payment->delete(); 
                    foreach ($entries as $key => $value) { 
                        $ent = \App\Models\Entry::find($value);
                        if(!empty($ent)){
                            $ent->delete();
                        }
                    }
                }else{
                    $payment->amount = 0;
                    $payment->update();
                    $total_paid = $payment->amount;
                    
                    
                    $final_amount = \App\Transaction::find($payment->transaction_id);
                    if(!empty($final_amount)){
                        $balance =  $final_amount->final_total;
                        $others  =  \App\TransactionPayment::where("id","!=",$id)->where("transaction_id",$payment->transaction_id)->where("return_payment",0)->sum("amount");
                        $total_paid += $others;
                        $status  =  'due';
                        if ($balance <= $total_paid) {
                            $status = 'paid';
                        } elseif ($total_paid > 0 && $balance > $total_paid) {
                            $status = 'partial';
                        }
                        $transaction_pay = \App\TransactionPayment::where("transaction_id",$payment->transaction_id)->where("return_payment",1)->where("id","!=",$payment->id)->first();
                        if(empty($transaction_pay)){
                            $final_amount->sub_type = null;
                        }
                        $final_amount->payment_status = $status;
                        $final_amount->update();
                    } 
                    
                    $payment->delete();
            
                } 
            }

            $data->delete();
        }
        DB::commit();
        return redirect('payment-voucher')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function effect_account($id,$type,$created_by=null)
    {
        # supplier debit  => bank  credit
        # customer credit => debit  
        $data      =  PaymentVoucher::find($id);
        $state     =  'debit';
        $re_state  =  'credit';
        if ($type == 1 ) {
            $state     =  'credit';
            $re_state  =  'debit';
        }
        # effect cash  account 
        $credit_data = [
            'amount'             => $data->amount,
            'account_id'         => $data->account_id,
            'type'               => $re_state,
            'sub_type'           => 'deposit',
            'operation_date'     => $data->date,
            'created_by'         => ($created_by != null)? $created_by:session()->get('user.id'),
            'note'               => $data->text,
            'payment_voucher_id' => $id
        ];
        $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
        $account = \App\Account::find($data->account_id);
        if($account->cost_center!= 1){
            \App\AccountTransaction::nextRecords($account->id,$data->business_id,$data->date);
        }
        # effect contact account 
        $account_id  = $data->contact_id;
        # $account_id  =  Contact::add_account($data->contact_id);
        $credit_data = [
            'amount'             => $data->amount,
            'account_id'         => $account_id,
            'type'               => $state,
            'sub_type'           => 'deposit',
            'operation_date'     => $data->date,
            'created_by'         => ($created_by != null)? $created_by:session()->get('user.id'),
            'note'               => $data->text,
            'payment_voucher_id' => $data->id
        ];
        $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
        $account = \App\Account::find($account_id);
        if($account->cost_center!= 1){
            \App\AccountTransaction::nextRecords($account->id,$data->business_id,$data->date);
        }
    } 
    public function show($id)
    {   
        $payment =  TransactionPayment::where("payment_voucher_id",$id)->get();
        $data    =  PaymentVoucher::find($id) ;
        return view('voucher_payments.show')
                ->with('data',$data) 
                ->with('payment',$payment);
    }
    public function attach($id)
    {
        $data        = PaymentVoucher::find($id);
        return view("voucher_payments.attach")->with(compact("data"));
    }
    public function entry($id)
    {
        $data    =  PaymentVoucher::find($id);
        $entry   =  Entry::where("voucher_id",$id)->get(); 
        $last    = [];
        foreach($entry as $en){
            $listed  = [];
            $allData =  \App\AccountTransaction::where('payment_voucher_id',$id)->where("entry_id",$en->id)->whereNull("for_repeat")->where('amount','>',0)->get();
            foreach($allData as $one){
                $listed[] = $one; 
            }
            $last[$en->refe_no_e] = $listed;
        }
         
        return view('voucher_payments.entry')
               ->with('allData',$allData)
               ->with('entry',$entry)
               ->with('last',$last)
               ->with('data',$data); 
    }
    public function returnVoucher($id) {
        try{
            \DB::beginTransaction();
            $data                = PaymentVoucher::find($id);
            $transaction_payment = \App\TransactionPayment::where("payment_voucher_id",$id)->first();
            $allDataDebit        = \App\AccountTransaction::where('payment_voucher_id',$id)->where("type","debit")->where('amount','>',0)->get();
            $allDataCredit       = \App\AccountTransaction::where('payment_voucher_id',$id)->where("type","credit")->where('amount','>',0)->get();
            $type                = "Return Voucher";
            \App\Models\Entry::create_entries($data,$type);
            $entry               = \App\Models\Entry::where("state","Return Voucher")->where("voucher_id",$id)->first();
            foreach($allDataDebit as $debit){
                $beta           =  $debit->replicate();
                $beta->type     = "credit";
                $beta->entry_id = $entry->id;
                $beta->save();
                $accountDebit   = \App\Account::find($debit->account_id);
                if($accountDebit->cost_center!= 1){
                    \App\AccountTransaction::nextRecords($accountDebit->id,$data->business_id,$debit->operation_date);
                }
            }
            foreach($allDataCredit as $credit){
                $beta           =  $credit->replicate();
                $beta->type     = "debit";
                $beta->entry_id = $entry->id;
                $beta->save();
                $accountCredit   = \App\Account::find($credit->account_id);
                if($accountCredit->cost_center!= 1){
                    \App\AccountTransaction::nextRecords($accountCredit->id,$data->business_id,$credit->operation_date);
                }

            }
            $data->return_voucher = 1;
            // $sum         = 0;
            $transaction_payment->return_payment = 1;
            $data->update();
            $transaction_payment->update();
            $TR              = \App\Transaction::find($transaction_payment->transaction_id);
            $TR->sub_type    = "return_payment";
            $TR->update();
            
            if($transaction_payment->is_invoice == 1){
                $parent          = \App\Transaction::find($TR->separate_parent);
                $total_parent    = $parent->final_total;
                $allTransaction  = \App\Transaction::where("id","!=",$TR->id)->whereNull("sub_type")->where("separate_parent",$TR->separate_parent)->get();
                foreach($allTransaction as $one_pay){
                    $sum        +=   $one_pay->final_total; 
                }
                if(!empty($total_parent)){
                    $parent_balance = $parent->final_total;
                    $status         = 'due';
                    if ($parent_balance <= $sum) {
                        $status     = 'paid';
                    } elseif ($sum > 0 && $parent_balance > $sum) {
                        $status     = 'partial';
                    }
                    $parent->payment_status = $status;
                    $parent->update();
                }
                $TR->payment_status = 'due';
                $TR->update();
                
            }else{
                $allPayment      = \App\TransactionPayment::where("transaction_id",$TR->id)->where("return_payment",0)->where("id","!=",$transaction_payment->id)->sum("amount");
                $total_parent    = $TR->final_total;
                $status          = 'due';
                if ($total_parent <= $allPayment) {
                    $status      = 'paid';
                } elseif ($allPayment > 0 && $total_parent > $allPayment) {
                    $status      = 'partial';
                }
                
                $TR->payment_status = $status;
                $TR->update();
            }
            \DB::commit();
            $output = [
                "success" => 1,
                "msg"     => __("Returned Successfully"),
            ];
        }catch(Exception $e){
            $output = [
                "success" => 0,
                "msg"     => __("Returned Failed"),
            ];
        }
        return $output;
    }
    public function set_message($id)
    {
        $data        = PaymentVoucher::find($id);
        $contact     = \App\Contact::find($data->contact_id);
        $country     = ["971","963" ,  "93",  "355",  "213",  "376",  "244",  "672",  "54",  "374",  "297",  "61",  "43",  "994",  "973",  "880",  "375",  "32",  "501",  "229",  "975",  "591",  "387",  "267",  "55",  "673",  "359",  "226",  "257",  "855",  "237",  "1",  "238",  "236",  "235",  "56",  "86",  "53",  "61",  "57",  "269",  "243",  "242",  "682",  "506",  "225",  "385",  "53",  "357",  "420",  "45",  "253",   "670",  "593",   "20",  "503",  "240",  "291",  "372",  "251",  "500",  "298",  "679",  "358",  "33",  "594",  "689",  "241",  "220",  "995",  "49",  "233",  "350",  "30",  "299",  "590",  "502",  "224",  "245",  "592",  "509",  "504",  "852",  "36",  "354",  "91",  "62",  "98",  "964",  "353",  "972",  "39",  "81",  "962",  "7",  "254",  "686",  "850",  "82",  "965",  "996",  "856",  "371",  "961",  "266",  "231",  "218",  "423",  "370",  "352",  "853",  "389",  "261",  "265",  "60",  "960",  "223",  "356",  "692",  "596",  "222",  "230",  "269",  "52",  "691",  "373",  "377",  "976",  "212",  "258",  "95",  "264",  "674",  "977",  "31",  "599",  "687",  "64",  "505",  "227",  "234",  "683",  "672",  "47",  "968",  "92",  "680",  "970",  "507",  "675",  "595",  "51",  "63",  "48",  "351",  "974",   "262",  "40",  "7",  "250",  "290",  "508",  "685",  "378",  "239",  "966",  "221",  "248",  "232",  "65",  "421",  "386",  "677",  "252",  "27",  "34",  "94",  "249",  "597",  "268",  "46",  "41",  "963",  "886",  "992",  "255",  "66",  "690",  "676",  "216",  "90",  "993",  "688",  "256",  "380",  "971",  "44",  "1",  "598",  "998",  "678",  "418",  "58",  "84",  "681",  "967",  "260",  "263",];
        $countries   = [];
        foreach($country as $item){
            $countries[] = [
                "id"   => $item,  
                "code" =>"+".$item  
            ];
        }
        
        return view("voucher_payments.whatsapp")->with(compact(["data","contact","countries"]));
    }
    public function choosePattern($id)
    {
        $item        = PaymentVoucher::find($id);
        $contact     = \App\Contact::find($item->contact_id);
        $patterns    = \App\Models\Pattern::get(); 
        return view("voucher_payments.PrintSelect")->with(compact(["item","contact","patterns"]));
    }

public function post_message(Request $request,$id)
    {
        if(request()->ajax()){
            if(request()->input("mobile") == null || request()->input("mobile") == ""){
                return false;
            }
            if(request()->input("code") == null || request()->input("code") == ""){
                return false;
            }
            $code  = request()->input("code");
            $mo    = request()->input("mobile");
            $phone=$code.$mo;
            $data_voucher = \App\Models\PaymentVoucher::find($id);
            $information  = "";
            $information .= " Ref : " . $data_voucher->ref_no ." ";
            $information .=  $data_voucher->amount . "AED" ;
 
            
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://graph.facebook.com/v18.0/179023578638088/messages',
            //   CURLOPT_URL => 'https://graph.facebook.com/v18.0/231433200054260/messages',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "messaging_product": "whatsapp",
                "to": '. $phone.',
                "type": "template",
                "template": {
                    "name": "izofuture",
                    "language": {
                        "code": "en"
                    },
                    "components": [
                            {
                                "type": "header",
                                "parameters": [
                                    {
                                        "type": "text",
                                        "text": "'.$information.'"
                                    }
                                ]
                            }
                        ]
                }
              }'
            ,
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer EAASq0IHkBgsBOzTHepkk33hP0eHAWZCL1QuIYJIzGKW4hr6XMblwklLisTsjccSZCaH23ELThqYrnc6gXhrq2RV9ZBavUmqkDrqymZAZAoLl9L1QS9eXxySCWReb9ZCPxX71Lvd9w8Um9DNMucF0aZASKZB1tGWm1ZB5jwqkRVMtnvFRloG7CQVqaja2fiD54DYiwcewmeNZBBOlLsX7BPsIlZApagEdKYEaEnybBX7iwZDZD',
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
             return $response;
        }
    }


}
